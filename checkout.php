<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_order_id'])) {
    die("Please log in to proceed.");
}

$user_order_id = $_SESSION['user_order_id'];

// Fetch cart items for the user
$query = "SELECT c.order_id AS cart_order_id, c.product_order_id, c.quantity, p.name, p.price, p.stock 
          FROM cart c
          JOIN products p ON c.product_order_id = p.order_id
          WHERE c.user_order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Your cart is empty.");
}

// Calculate total price
$total_price = 0;
$order_items = [];
while ($row = $result->fetch_assoc()) {
    $total_price += $row['price'] * $row['quantity'];
    $order_items[] = $row;
}

// Insert into orders table (single order order_id)
$order_query = "INSERT INTO orders (user_order_id, total_price) VALUES (?, ?)";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("order_id", $user_order_id, $total_price);
$stmt->execute();
$order_id = $stmt->insert_order_id; // Get the generated order order_id

// Insert into order_items table & update stock
foreach ($order_items as $item) {
    $insert_item_query = "INSERT INTO order_items (order_id, product_order_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_item_query);
    $stmt->bind_param("iiorder_id", $order_id, $item['product_order_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}


    // Insert into order_items
    $insert_item_query = "INSERT INTO order_items (order_id, product_order_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_item_query);
    $stmt->bind_param("iiorder_id", $order_id, $item['product_order_id'], $item['quantity'], $item['price']);
    $stmt->execute();

    // Reduce stock
    $update_stock_query = "UPDATE products SET stock = stock - ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_stock_query);
    $stmt->bind_param("ii", $item['quantity'], $item['product_order_id']);
    $stmt->execute();


// Clear the cart
$clear_cart_query = "DELETE FROM cart WHERE user_order_id = ?";
$stmt = $conn->prepare($clear_cart_query);
$stmt->bind_param("i", $user_order_id);
$stmt->execute();

// Redirect to payment page
header("Location: payment.php?order_id=$order_id");
exit();
?>
