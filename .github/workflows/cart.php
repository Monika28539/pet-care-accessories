<?php
require_once 'auth.php';
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();

// Get cart items
$cart_sql = "SELECT c.cart_id, c.product_id, p.name, p.price, p.image_url, c.quantity 
             FROM cart c 
             JOIN products p ON c.product_id = p.product_id 
             WHERE c.user_id = ?
             ORDER BY c.added_at DESC";

$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PetCare Store | Cart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-section {
            padding: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .cart-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .cart-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls button {
            padding: 5px 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 3px;
        }

        .quantity-controls input {
            width: 50px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .remove-btn {
            padding: 8px 12px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background: #ff4d4d;
        }

        .cart-total {
            text-align: right;
            margin-bottom: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .cart-total h3 {
            font-size: 24px;
            margin: 0 0 15px 0;
        }

        .checkout-btn {
            padding: 12px 30px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .checkout-btn:hover {
            background: #218838;
        }

        .continue-shopping {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .continue-shopping:hover {
            background: #0056b3;
        }

        .payment-box {
            margin-top: 20px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 10px;
        }

        .payment-option {
            margin: 10px 0;
            padding: 12px;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #ddd;
        }

        .payment-option:hover {
            background: #e8f7ff;
        }

        .payment-option input {
            margin-right: 10px;
        }

        .confirm-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .confirm-btn:hover {
            background: #218838;
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
        <span style="color: #0c0c0c; margin-left: 20px;">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
        <a href="logout.php" style="margin-left: 10px;">Logout</a>
    </nav>
</header>

<section class="cart-section">
    <h2>Shopping Cart</h2>

    <a href="products.php" class="continue-shopping">← Continue Shopping</a>

    <?php if (count($cart_items) === 0): ?>
        <div class="empty-cart">
            <h3>Your cart is empty</h3>
            <p><a href="products.php">Start shopping</a></p>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-img">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="quantity-controls">
                                <button onclick="decreaseQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity']; ?>)">-</button>
                                <input type="number" id="qty-<?php echo $item['cart_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                                <button onclick="increaseQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity']; ?>)">+</button>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <button class="remove-btn" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-total">
            <h3>Total: $<span id="totalAmount"><?php echo number_format($total, 2); ?></span></h3>
            <button class="checkout-btn" onclick="showPayment()">Proceed to Checkout</button>
        </div>

        <!-- PAYMENT METHODS -->
        <div class="payment-box" id="paymentBox" style="display: none;">
            <h2>Select Payment Method</h2>
            <form id="paymentForm">
                <div class="payment-option">
                    <input type="radio" name="payment" id="card" value="Card" required>
                    <label for="card">💳 Credit / Debit Card</label>
                </div>

                <div class="payment-option">
                    <input type="radio" name="payment" id="upi" value="UPI" required>
                    <label for="upi">📱 UPI Payment</label>
                </div>

                <div class="payment-option">
                    <input type="radio" name="payment" id="banking" value="Banking" required>
                    <label for="banking">🏦 Net Banking</label>
                </div>

                <div class="payment-option">
                    <input type="radio" name="payment" id="cod" value="COD" required>
                    <label for="cod">💵 Cash on Delivery</label>
                </div>

                <button type="button" class="confirm-btn" onclick="completeCheckout()">Complete Order</button>
            </form>
        </div>
    <?php endif; ?>
</section>

<footer>
    <p>© 2026 PetCare Store | All Rights Reserved</p>
</footer>

<script>
function showPayment() {
    const paymentBox = document.getElementById('paymentBox');
    paymentBox.style.display = paymentBox.style.display === 'none' ? 'block' : 'none';
}

function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item?')) {
        const formData = new FormData();
        formData.append('action', 'remove_from_cart');
        formData.append('cart_id', cartId);

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;

    const formData = new FormData();
    formData.append('action', 'update_quantity');
    formData.append('cart_id', cartId);
    formData.append('quantity', newQuantity);

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function increaseQuantity(cartId, currentQuantity) {
    updateQuantity(cartId, currentQuantity + 1);
}

function decreaseQuantity(cartId, currentQuantity) {
    if (currentQuantity > 1) {
        updateQuantity(cartId, currentQuantity - 1);
    }
}

function completeCheckout() {
    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    if (!selectedPayment) {
        alert('Please select a payment method');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'checkout');
    formData.append('payment_method', selectedPayment.value);

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully!\nOrder ID: ' + data.order_id + '\nTotal: $' + data.total_amount);
            window.location.href = 'products.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
