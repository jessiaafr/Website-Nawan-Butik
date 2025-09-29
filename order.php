<?php
include 'koneksi.php';


if (isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $pelanggan_id = $_SESSION['pelanggan_id']; // Gunakan ID pelanggan yang sudah login

    // Query untuk menambahkan data ke tabel cart
    $query = "INSERT INTO cart (pelanggan_id, product_id, product_name, product_price, quantity)
              VALUES (?, ?, ?, ?, 1)"; // Asumsikan quantity adalah 1 saat pertama kali menambahkan produk ke keranjang

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $pelanggan_id, $product_id, $product_name, $product_price);
    if ($stmt->execute()) {
        echo "Item added to cart successfully";
    } else {
        echo "Error adding item to cart";
    }
    exit();
}

// Query produk dan kategori
$product_query = "SELECT p.*, c.category_name 
                 FROM product p 
                 LEFT JOIN category c ON p.category_id = c.category_id 
                 WHERE p.product_status = 'Available' 
                 ORDER BY c.category_name, p.product_name";
$products_result = $conn->query($product_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkiNini - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="images/logonawan.jpg" type="image/jpg">
    <style>
        /* Styling for the menu and shopping cart */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 600;
            color: #2c3e50;
        }
        .nav-link {
            color: #2c3e50;
            font-weight: 500;
        }
        .cart-icon {
            font-size: 1.5rem;
        }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('path_to_hero_image.jpg');
            background-size: cover;
            color: white;
            padding: 60px 0;
        }
        .category-filter {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .product-info {
            padding: 20px;
        }
        .product-price {
            color: #27ae60;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .btn-cart {
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">AkiNini</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="produk.php">Menu</a></li>
                </ul>
                <button class="btn btn-link" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class="fas fa-shopping-cart cart-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1>Our Menu</h1>
            <p class="lead">Discover our delicious selection of traditional dishes</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Products Grid -->
        <div class="row" id="productsGrid">
            <?php while ($product = $products_result->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6 product-item">
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($product['product_image']); ?>" 
                         class="product-image" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <div class="product-info">
                        <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                        <div class="product-price">Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></div>
                        <button class="btn btn-cart" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>', <?php echo $product['product_price']; ?>)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Your Cart</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="cartItems">
            <!-- Cart items will be displayed here -->
        </div>
        <div class="offcanvas-footer">
            <button class="btn btn-success" onclick="proceedToCheckout()">Proceed to Checkout</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];

        function addToCart(productId, productName, productPrice) {
            cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
            updateCart();
        }

        function updateCart() {
            const cartItemsContainer = document.getElementById('cartItems');
            cartItemsContainer.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                total += item.price * item.quantity;
                const cartItemElement = document.createElement('div');
                cartItemElement.innerHTML = `${item.name} - Rp ${item.price} x ${item.quantity}`;
                cartItemsContainer.appendChild(cartItemElement);
            });

            const totalPriceElement = document.createElement('div');
            totalPriceElement.textContent = `Total: Rp ${total}`;
            cartItemsContainer.appendChild(totalPriceElement);
        }

        function proceedToCheckout() {
            // Save the order data to database using AJAX or form submission to checkout.php
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
