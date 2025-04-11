<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in to add products to the cart.");
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// Check if product already exists in the cart
$check_query = "SELECT id FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity if product is already in cart
    $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    echo "Product quantity updated in cart.";
} else {
    // Insert new product into cart
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "Product added to cart successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
