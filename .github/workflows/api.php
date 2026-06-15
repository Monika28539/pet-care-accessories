<?php
/**
 * API Endpoints for AJAX Requests
 * Handles cart operations, product data, and other dynamic requests
 */

require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Get all products
if ($action === 'get_products') {
    $sql = "SELECT product_id, name, price, image_url FROM products";
    $result = $conn->query($sql);
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}

// Get cart items for logged-in user
if ($action === 'get_cart') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    $user_id = getCurrentUserId();
    $sql = "SELECT c.cart_id, c.product_id, p.name, p.price, p.image_url, c.quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    
    echo json_encode(['success' => true, 'cart' => $cart]);
    exit;
}

// Add to cart
if ($action === 'add_to_cart') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $user_id = getCurrentUserId();
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit;
    }
    
    // Check if product exists
    $check_stmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $check_stmt->bind_param("i", $product_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    // Check if item already in cart
    $check_cart = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_cart->bind_param("ii", $user_id, $product_id);
    $check_cart->execute();
    $cart_result = $check_cart->get_result();
    
    if ($cart_result->num_rows > 0) {
        // Update quantity
        $row = $cart_result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
    } else {
        // Insert new item
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
    exit;
}

// Remove from cart
if ($action === 'remove_from_cart') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    $user_id = getCurrentUserId();
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    
    if ($cart_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
        exit;
    }
    
    $delete_stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($delete_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
    exit;
}

// Update cart quantity
if ($action === 'update_quantity') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    $user_id = getCurrentUserId();
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($cart_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
    $update_stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
    }
    exit;
}

// Process checkout
if ($action === 'checkout') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    $user_id = getCurrentUserId();
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    
    if (empty($payment_method)) {
        echo json_encode(['success' => false, 'message' => 'Payment method required']);
        exit;
    }
    
    // Get cart items
    $cart_sql = "SELECT c.product_id, p.price, c.quantity 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.product_id 
                 WHERE c.user_id = ?";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    // Calculate total
    $total_amount = 0;
    $cart_items = [];
    while ($row = $cart_result->fetch_assoc()) {
        $total_amount += $row['price'] * $row['quantity'];
        $cart_items[] = $row;
    }
    
    // Create order
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, order_status) VALUES (?, ?, ?, ?)");
    $status = 'pending';
    $order_stmt->bind_param("idss", $user_id, $total_amount, $payment_method, $status);
    
    if (!$order_stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to create order']);
        exit;
    }
    
    $order_id = $conn->insert_id;
    
    // Add order items
    foreach ($cart_items as $item) {
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $item_stmt->bind_param("iid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $item_stmt->execute();
    }
    
    // Clear cart
    $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'total_amount' => $total_amount
    ]);
    exit;
}

// Submit contact form
if ($action === 'submit_contact') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    $contact_stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $contact_stmt->bind_param("sss", $name, $email, $message);
    
    if ($contact_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
