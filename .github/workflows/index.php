<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PetCare | Home</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="top-bar">
    <div class="logo">🐾 PetCare </div>
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

<section class="hero">
    <div class="hero-left">
        <h1>Everything Your Pet Needs Delivered to Your Home</h1>
        <p>Food • Toys • Accessories • Care Products</p>

        <div class="hero-buttons">
            <a href="products.php" class="btn-primary">Shop now</a>
        </div>
    </div>

    <div class="hero-right">
        <img src="https://cdn.pixabay.com/photo/2018/10/01/09/21/pets-3715733_960_720.jpg" alt="Pet Box">
    </div>
</section>

<footer>
    <p>© 2026 PetCare Store | All Rights Reserved</p>
</footer>

</body>
</html>
