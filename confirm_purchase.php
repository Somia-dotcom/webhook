<?php
session_start();
include 'db.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please log in to proceed"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Fetch product details
$query = "SELECT stock, price FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product || $quantity > $product['stock']) {
    echo json_encode(["success" => false, "message" => "Invalid purchase quantity"]);
    exit();
}

// Calculate total price
$total_price = $quantity * $product['price'];

// Start transaction
$conn->begin_transaction();

try {
    // Insert order into `orders` table (Fixed)
    $order_query = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();

    // Reduce stock in `products` table
    $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($update_stock);
    $stmt->bind_param("ii", $quantity, $product_id);
    $stmt->execute();

    // Get updated stock value
    $updated_stock = $product['stock'] - $quantity;

    // Commit the transaction
    $conn->commit();

    echo json_encode(["success" => true, "new_stock" => $updated_stock]);
} catch (Exception $e) {
    $conn->rollback(); // Rollback changes on error
    echo json_encode(["success" => false, "message" => "Failed to process purchase"]);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer


include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to make a purchase.");
}

$user_id = $_SESSION['user_id']; // Buyer ID
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Fetch product and farmer details
$query = "SELECT p.*, u.email AS farmer_email, u.name AS farmer_name 
          FROM products p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product || $quantity > $product['stock']) {
    die("Invalid purchase quantity.");
}

// Calculate total price
$total_price = $quantity * $product['price'];

// Start transaction
$conn->begin_transaction();

try {
    // Insert order into `orders` table
    $order_query = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();

    // Reduce stock in `products` table
    $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($update_stock);
    $stmt->bind_param("ii", $quantity, $product_id);
    $stmt->execute();

    // Get updated stock value
    $updated_stock = $product['stock'] - $quantity;

    // Fetch buyer details
    $buyer_query = "SELECT name, email, phone FROM users WHERE id = ?";
    $stmt = $conn->prepare($buyer_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $buyer_result = $stmt->get_result();
    $buyer = $buyer_result->fetch_assoc();

    // Commit the transaction
    $conn->commit();

    // Send Email to Farmer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server (e.g., Gmail)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cssomiab@gmail.com'; // Your email
        $mail->Password   = 'dlbz cvsl uzep exev'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
    
        $mail->setFrom('cssomiab@gmail.com', 'AgroBazaar');
        $mail->addAddress($product['farmer_email'], $product['farmer_name']);

        $mail->Subject = 'Your Product Has Been Purchased!';
        $mail->Body    = "Hello " . $product['farmer_name'] . ",\n\n" .
                         "Your product **" . $product['name'] . "** has been purchased by:\n\n" .
                         "**Buyer Name:** " . $buyer['name'] . "\n" .
                         "**Buyer Email:** " . $buyer['email'] . "\n" .
                         "**Buyer Phone:** " . $buyer['phone'] . "\n" .
                         "**Quantity Purchased:** " . $quantity . "\n" .
                         "**Remaining Stock:** " . $updated_stock . "\n\n" .
                         "Please process the order accordingly.\n\n" .
                         "Best Regards,\nAgroBazaar Team";

        $mail->send();
        echo "<script>alert('Purchase successful! Email sent to the farmer.'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Purchase successful, but email failed to send. Error: {$mail->ErrorInfo}'); window.location.href='index.php';</script>";
    }

} catch (Exception $e) {
    $conn->rollback(); // Rollback changes on error
    echo "<script>alert('Failed to process purchase!'); window.location.href='index.php';</script>";
}

?>