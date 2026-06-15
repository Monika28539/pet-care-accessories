<?php
require_once 'auth.php';

$message = '';
$error = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PetCare Store | Contact</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .contact-section {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .contact-section h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            font-family: Arial, sans-serif;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #4da3f0;
            box-shadow: 0 0 5px rgba(77, 163, 240, 0.3);
        }

        button {
            padding: 12px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background: #ff4d4d;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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

<section class="contact-section">
    <h2>Contact Us</h2>

    <?php if (!empty($message)): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="contactForm">
        <input type="text" id="name" name="name" placeholder="Your Name" required>
        <input type="email" id="email" name="email" placeholder="Your Email" required>
        <textarea id="message" name="message" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<footer>
    <p>© 2026 PetCare Store | All Rights Reserved</p>
</footer>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    const formData = new FormData();
    formData.append('action', 'submit_contact');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('message', message);

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('contactForm').reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending your message');
    });
});
</script>

</body>
</html>
