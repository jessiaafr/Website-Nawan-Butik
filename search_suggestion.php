<?php
include 'koneksi.php';

$search = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

if ($search !== '') {
    $query = "SELECT product_id, product_name FROM product WHERE product_name LIKE '%$search%' LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
}
?>

