<?php
session_start();
include 'db.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['cart_id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
        exit();
    }

    $cart_id = intval($_POST['cart_id']); // Sanitize input

    // Delete item from cart
    $delete_query = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $cart_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Item removed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove item"]);
    }

    $stmt->close();
    $conn->close();
}
?>
