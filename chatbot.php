<?php
include 'koneksi.php';
header('Content-Type: application/json');

$userMessage = strtolower($_POST['message'] ?? '');
$response = ['text' => '', 'products' => []];

// Stopwords
$stopwords = ['dan', 'atau', 'yang', 'untuk', 'dengan', 'di', 'ke', 'pada'];
$keywords = array_filter(explode(' ', $userMessage), function ($word) use ($stopwords) {
    return strlen($word) > 2 && !in_array($word, $stopwords);
});

if (count($keywords) > 0) {
    $totalDocs = (int)$conn->query("SELECT COUNT(*) AS total FROM product")->fetch_assoc()['total'];
    error_log("Total Dokumen: $totalDocs");

    // Hitung IDF dan log DF
    $idfMap = [];
    foreach ($keywords as $word) {
        $safeWord = $conn->real_escape_string($word);
        $dfRow = $conn->query("SELECT COUNT(*) AS df FROM product WHERE LOWER(product_description) LIKE '%$safeWord%'")->fetch_assoc();
        $df = (int)$dfRow['df'];
        $idfMap[$word] = log10($totalDocs / (1 + $df));
        error_log("Kata '$word' → DF: $df, IDF: " . round($idfMap[$word], 4));

        // Tambahkan log produk apa saja yang mengandung kata ini
        $produkQuery = $conn->query("SELECT product_id, product_name FROM product WHERE LOWER(product_description) LIKE '%$safeWord%'");
        if ($produkQuery && $produkQuery->num_rows > 0) {
            error_log("Produk yang mengandung kata '$word':");
            while ($prod = $produkQuery->fetch_assoc()) {
                error_log("  - [{$prod['product_id']}] {$prod['product_name']}");
            }
        } else {
            error_log("Tidak ditemukan produk yang mengandung kata '$word'");
        }
    }

    $dotProductParts = [];
    $docVectorLengthParts = [];
    $queryVectorLengthSquared = 0;

    foreach ($keywords as $word) {
        $idf = $idfMap[$word];
        $safeWord = $conn->real_escape_string($word);

        // TF = jumlah kata t dalam dokumen / jumlah total kata dalam dokumen
        $tf = "(
            (
                LENGTH(CONCAT(' ', LOWER(product_description), ' ')) - 
                LENGTH(REPLACE(CONCAT(' ', LOWER(product_description), ' '), ' $safeWord ', ''))
            ) / LENGTH(' $safeWord ')
        ) / (
            LENGTH(TRIM(product_description)) - LENGTH(REPLACE(TRIM(product_description), ' ', '')) + 1
        )";

        $tfidfDoc = "($tf * $idf)";
        $tfidfQuery = $idf;

        $dotProductParts[] = "($tfidfDoc * $tfidfQuery)";
        $docVectorLengthParts[] = "POWER($tfidfDoc, 2)";
        $queryVectorLengthSquared += pow($tfidfQuery, 2);
    }

    $dotProduct = implode(' + ', $dotProductParts);
    $docVectorLength = "SQRT(" . implode(' + ', $docVectorLengthParts) . ")";
    $queryVectorLength = sqrt($queryVectorLengthSquared);
    $cosineSimilarity = "($dotProduct) / ($docVectorLength * $queryVectorLength)";

    // Tambahan: Debug Query Vector
    error_log("==== TF-IDF QUERY VECTOR ====");
    foreach ($keywords as $word) {
        $idf = $idfMap[$word];
        error_log("  $word → TF-IDF Query: " . round($idf, 4));
    }
    error_log("Query Vector Length Squared: " . round($queryVectorLengthSquared, 4));
    error_log("Query Vector Length (akar): " . round($queryVectorLength, 4));

    // Debug fields
    $debugFields = [];
    foreach ($keywords as $word) {
        $idf = $idfMap[$word];
        $safeWord = $conn->real_escape_string($word);
        $tf = "(
            (
                LENGTH(CONCAT(' ', LOWER(product_description), ' ')) - 
                LENGTH(REPLACE(CONCAT(' ', LOWER(product_description), ' '), ' $safeWord ', ''))
            ) / LENGTH(' $safeWord ')
        ) / (
            LENGTH(TRIM(product_description)) - LENGTH(REPLACE(TRIM(product_description), ' ', '')) + 1
        )";
        $tfidf = "($tf * $idf)";
        $debugFields[] = "$tf AS tf_$safeWord";
        $debugFields[] = "$idf AS idf_$safeWord";
        $debugFields[] = "$tfidf AS tfidf_$safeWord";
    }
    $debugFieldsSql = implode(",\n                ", $debugFields);

    $query = "SELECT product_id, category_id, product_name, product_price, product_discount_price,
                     product_description, product_image, product_stock, data_created,
                     $cosineSimilarity AS cosine_score, $debugFieldsSql
              FROM product
              HAVING cosine_score > 0
              ORDER BY cosine_score DESC
              LIMIT 10";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $response['text'] = "Berikut produk yang paling relevan dengan pencarian Anda:";
        while ($row = $result->fetch_assoc()) {
            $row['formatted_price'] = 'Rp' . number_format($row['product_price'], 0, ',', '.');
            $row['formatted_date'] = date('d M Y', strtotime($row['data_created']));
            $row['stock_status'] = $row['product_stock'] > 0
                ? '<span class="text-success">Tersedia (' . $row['product_stock'] . ')</span>'
                : '<span class="text-danger">Habis</span>';
            $row['full_image_path'] = 'uploads/' . $row['product_image'];
            $row['product_link'] = 'detail_produk.php?id=' . $row['product_id'];
            $row['cosine_score'] = round($row['cosine_score'], 4);

            error_log("Cosine Score produk {$row['product_name']}: {$row['cosine_score']}");
            foreach ($keywords as $word) {
                $tfVal = round($row["tf_$word"], 4);
                $idfVal = round($row["idf_$word"], 4);
                $tfidfVal = round($row["tfidf_$word"], 4);
                error_log("  $word → TF: $tfVal, IDF: $idfVal, TF-IDF: $tfidfVal");
            }

            $response['products'][] = $row;
        }
    } else {
        $response['text'] = "Maaf, tidak ditemukan produk yang cocok: <strong>" . htmlspecialchars($userMessage) . "</strong>";
    }
} else {
    $response['text'] = "Mohon masukkan keluhan yang lebih spesifik.";
}

echo json_encode($response);
$conn->close();