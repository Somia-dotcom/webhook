<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id'];
$payment_id = $_GET['payment_id'] ?? '';

if ($payment_id) {
    // Insert payment details into the database
    $stmt = $conn->prepare("INSERT INTO payments (user_id, payment_id, amount, status) VALUES (?, ?, ?, ?)");
    $amount = 100; // Replace with actual amount
    $status = "Success";
    $stmt->bind_param("isds", $user_id, $payment_id, $amount, $status);
    
    if ($stmt->execute()) {
        echo "Payment Successful! Your Payment ID is: " . $payment_id;
    } else {
        echo "Database error: " . $stmt->error;
    }
} else {
    echo "Payment failed!";
}
?>
