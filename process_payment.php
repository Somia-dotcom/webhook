<?php
session_start();
include 'db.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to proceed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $id = intval($_POST['id']);
    $product_id = intval($_POST['product_id']);
    $amount = floatval($_POST['amount']);
    $payment_method = $_POST['payment_method'];

    // ✅ Check if the order exists
    $order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    if ($order_result->num_rows == 0) {
        die("Invalid order. Order not found.");
    }

    // ✅ Insert payment record
    $payment_query = "INSERT INTO payments (id, user_id, product_id, amount, payment_method, status) 
                      VALUES (?, ?, ?, ?, ?, 'Completed')";
    $stmt = $conn->prepare($payment_query);
    $stmt->bind_param("iiids", $id, $user_id, $product_id, $amount, $payment_method);

    if ($stmt->execute()) {
        // ✅ Update order status to 'Completed'
        $update_order = "UPDATE orders SET status = 'Completed' WHERE id = ?";
        $stmt = $conn->prepare($update_order);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "Payment successful!";
    } else {
        echo "Error processing payment: " . $stmt->error;
    }
}
if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    // Verify Razorpay Payment (Use Razorpay API)
    $verify_url = "https://api.razorpay.com/v1/payments/$payment_id";
    $ch = curl_init($verify_url);
    curl_setopt($ch, CURLOPT_USERPWD, "YOUR_RAZORPAY_KEY_ID:YOUR_RAZORPAY_SECRET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $payment_data = json_decode($response, true);
    if ($payment_data['status'] === "captured") {
        echo "Payment Successful! Transaction ID: " . $payment_id;
    } else {
        echo "Payment Failed!";
    }
}
?>
