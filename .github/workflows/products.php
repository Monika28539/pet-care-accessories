<?php
require_once 'auth.php';
require_once 'config.php';

// Fetch products from database
$products_sql = "SELECT product_id, name, price, image_url FROM products";
$products_result = $conn->query($products_sql);
$products = [];

while ($row = $products_result->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PetCare Store | Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">🐾 PetCare Store</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="contact.php">Contact</a>
        <a href="cart.php">Cart</a>
        <?php if (isLoggedIn()): ?>
            <span style="color: #0c0c0c; margin-left: 20px;">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
            <a href="logout.php" style="margin-left: 10px;">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-signin">Login</a>
        <?php endif; ?>
    </nav>
</header>

<section class="products-page">
    <h2>Our Products</h2>
    
    <?php if (!isLoggedIn()): ?>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
            <p><strong>Please <a href="login.php">login</a> to add items to cart</strong></p>
        </div>
    <?php endif; ?>
    
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>$<?php echo htmlspecialchars($product['price']); ?></p>
                <button onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')" 
                        class="cart-btn" <?php if (!isLoggedIn()) echo 'disabled'; ?>>Add to Cart</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function addToCart(productId, productName) {
    <?php if (!isLoggedIn()): ?>
        alert('Please login first to add items to cart');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    
    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(productName + ' added to cart');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<footer>
    <p>© 2026 PetCare Store | All Rights Reserved</p>
</footer>

</body>
</html>
