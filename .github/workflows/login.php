<?php
require_once 'auth.php';

$error = '';
$success = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'login') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            header("Location: products.php");
            exit;
        } else {
            $error = $result['message'];
        }
    } 
    elseif ($action === 'register') {
        $reg_username = isset($_POST['reg_username']) ? trim($_POST['reg_username']) : '';
        $reg_email = isset($_POST['reg_email']) ? trim($_POST['reg_email']) : '';
        $reg_password = isset($_POST['reg_password']) ? $_POST['reg_password'] : '';
        $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        
        $result = registerUser($reg_username, $reg_email, $reg_password, $full_name);
        
        if ($result['success']) {
            $success = $result['message'] . " You can now login.";
        } else {
            $error = $result['message'];
        }
    }
}

// If already logged in, redirect to products
if (isLoggedIn()) {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Login - PetCare Store</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #36f0f7, #2752df, #e723c7);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            display: flex;
            gap: 20px;
            max-width: 900px;
            width: 90%;
        }

        .login-box, .register-box {
            background: #f2f2f2;
            width: 320px;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-bottom: 30px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .password-box {
            position: relative;
        }

        .password-box input {
            padding-right: 45px;
        }

        .password-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: black;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #4da3f0;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: #357abd;
        }

        .message {
            margin-top: 15px;
            font-size: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .link-text {
            margin-top: 15px;
            font-size: 14px;
        }

        .link-text a {
            color: #4da3f0;
            text-decoration: none;
            cursor: pointer;
        }

        .link-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <!-- Login Box -->
    <div class="login-box">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <input type="text" name="username" placeholder="Username" required>

            <div class="password-box">
                <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                <i class="fas fa-eye" onclick="togglePassword('loginPassword')"></i>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="link-text">
            Don't have an account? <a onclick="toggleForms()">Register here</a>
        </div>
    </div>

    <!-- Register Box -->
    <div class="register-box" id="registerBox" style="display: none;">
        <h2>Register</h2>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($error) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register'): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="reg_username" placeholder="Username" required>
            <input type="email" name="reg_email" placeholder="Email" required>

            <div class="password-box">
                <input type="password" id="regPassword" name="reg_password" placeholder="Password" required>
                <i class="fas fa-eye" onclick="togglePassword('regPassword')"></i>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="link-text">
            Already have an account? <a onclick="toggleForms()">Login here</a>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}

function toggleForms() {
    const loginBox = document.querySelector('.login-box');
    const registerBox = document.getElementById('registerBox');
    
    loginBox.style.display = loginBox.style.display === 'none' ? 'block' : 'none';
    registerBox.style.display = registerBox.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>
