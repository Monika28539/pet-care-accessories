<?php
/**
 * Authentication Functions
 * Handles user login, registration, and session management
 */

session_start();

// Include database configuration
require_once 'config.php';

/**
 * Register a new user
 */
function registerUser($username, $email, $password, $full_name) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    // Check if username already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $username, $email, $password_hash, $full_name);
    
    if ($insert_stmt->execute()) {
        return ['success' => true, 'message' => 'User registered successfully'];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . $conn->error];
    }
}

/**
 * Login user
 */
function loginUser($username, $password) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password are required'];
    }
    
    // Get user from database
    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    
    return ['success' => true, 'message' => 'Login successful', 'user_id' => $user['user_id']];
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    header("Location: login.php");
    exit();
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current username
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

?>
