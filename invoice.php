<?php
session_start();
include("db.php"); // Ensure you have a database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "Order ID is missing!";
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order details
$order_query = "SELECT o.id, o.total_price, o.order_date, o.status, u.name, u.email, u.phone, u.address
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    echo "Invalid order!";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch ordered items with correct price
$items_query = "SELECT oi.product_id, p.name, oi.quantity, p.price 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .invoice-container { max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; }
        .invoice-header { text-align: center; }
        .invoice-header h2 { margin: 0; }
        .invoice-details { margin-top: 20px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .invoice-total { margin-top: 20px; text-align: right; }

       
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    text-align: center;
}

/* Navigation Bar */
nav {
    background: #2c3e50;
    padding: 10px 0;
}
nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 0;
}
nav ul li {
    margin: 0 15px;
}
nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    padding: 10px 15px;
    transition: background 0.3s;
}
nav ul li a:hover {
    background: #34495e;
    border-radius: 5px;
}

/* Product Details */
.product-details {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}
.product-details img {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 5px;
}
.product-details h3 {
    margin: 10px 0;
    color: #2c3e50;
}
.product-details p {
    font-size: 16px;
    color: #555;
}
.product-card {
    border: 1px solid #ddd;
    padding: 10px;
    width: 200px;
    text-align: center;
    margin: 10px;
}
.product-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

/* Form Styles */
form {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}
form label {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}
form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}
form button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
}
form button:hover {
    background: #218838;
}

/* Responsive Design */
@media (max-width: 600px) {
    .product-details,
    form {
        width: 90%;
    }
}
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    text-align: center;
}

/* Navigation Bar */
nav {
    background: #2c3e50;
    padding: 10px 0;
}
nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 0;
}
nav ul li {
    margin: 0 15px;
}
nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    padding: 10px 15px;
    transition: background 0.3s;
}
nav ul li a:hover {
    background: #34495e;
    border-radius: 5px;
}

/* Purchase Confirmation Section */
.product-details {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}
.product-details img {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 5px;
}
.product-details h3 {
    margin: 10px 0;
    color: #2c3e50;
}
.product-details p {
    font-size: 16px;
    color: #555;
}

/* Stock Display */
.stock-info {
    font-size: 18px;
    font-weight: bold;
    color: #d9534f;
}

 /* Service Cards */
 .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .card-body {
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
        }
        .card-text {
            font-size: 14px;
            color: #555;
        }

/* Form Styles */
form {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}
form label {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}
form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}
form button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
}
form button:hover {
    background: #218838;
}

/* Purchase Success Popup */
.purchase-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    z-index: 1000;
}
.purchase-popup h3 {
    color: #28a745;
}
.purchase-popup button {
    background: #007bff;
    padding: 10px 20px;
    color: white;
    border: none;
    border-radius: 5px;
    margin-top: 10px;
    cursor: pointer;
}
.purchase-popup button:hover {
    background: #0056b3;
}

/* Responsive Design */
@media (max-width: 600px) {
    .product-details,
    form {
        width: 90%;
    }
}


    </style>
</head>
<body>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="report.php">Report</a></li>
        <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
        
    </ul>
    
</nav>
<br>
<br>
<div class="invoice-container">
    <div class="invoice-header">
        <h2>AgroBazaar Invoice</h2>
        <p>Order ID: <?php echo $order['id']; ?></p>
        <p>Date: <?php echo $order['order_date']; ?></p>
    </div>

    <div class="invoice-details">
        <p><strong>Customer Details:</strong></p>
        <p>Name: <?php echo $order['name']; ?></p>
        <p>Email: <?php echo $order['email']; ?></p>
        <p>Phone: <?php echo $order['phone']; ?></p>
        <p>Address: <?php echo $order['address']; ?></p>
    </div>

    <table class="invoice-table">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price (₹)</th>
            <th>Total (₹)</th>
        </tr>
        <?php
        $total_amount = 0;
        while ($item = $items_result->fetch_assoc()) {
            $subtotal = $item['quantity'] * $item['price'];
            $total_amount += $subtotal;
            echo "<tr>
                    <td>{$item['name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>₹{$item['price']}</td>
                    <td>₹{$subtotal}</td>
                  </tr>";
        }
        ?>
    </table>

    <div class="invoice-total">
        <h3>Total Amount: ₹<?php echo $total_amount; ?></h3>
    </div>
</div>

</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If using Composer
// require 'path-to-PHPMailer/PHPMailer.php'; // If manually downloaded

$mail = new PHPMailer(true);

try {
    // Fetch farmer details and remaining stock
    $farmer_query = "SELECT u.email AS farmer_email, u.name AS farmer_name, u.phone AS farmer_phone, 
                            p.name AS product_name, (p.stock - oi.quantity) AS remaining_stock
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     JOIN users u ON p.user_id = u.id
                     WHERE oi.order_id = ?";
    $stmt = $conn->prepare($farmer_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $farmer_result = $stmt->get_result();

    while ($farmer = $farmer_result->fetch_assoc()) {
        $farmer_email = $farmer['farmer_email'];
        $farmer_name = $farmer['farmer_name'];
        $product_name = $farmer['product_name'];
        $remaining_stock = $farmer['remaining_stock'];

        // Email content
        $subject = "Stock Update: $product_name Sold on AgroBazaar";
        $message = "
            <h2>Stock Update Notification</h2>
            <p>Dear <strong>$farmer_name</strong>,</p>
            <p>Your product '<strong>$product_name</strong>' has been purchased.</p>
            <h3>Buyer's Details:</h3>
            <p><strong>Name:</strong> {$order['name']}<br>
            <strong>Email:</strong> {$order['email']}<br>
            <strong>Phone:</strong> {$order['phone']}</p>
            <h3>Stock Update:</h3>
            <p>Remaining Stock: <strong>$remaining_stock</strong></p>
            <p>Regards, <br><strong>AgroBazaar Team</strong></p>
        ";

        // PHPMailer SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Change this for your SMTP provider
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cssomiab@gmail.com';  // Your email
        $mail->Password   = 'dlbz cvsl uzep exev';   // Your email password (use App Password for Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('cssomiab@gmail.com', 'AgroBazaar');
        $mail->addAddress($farmer_email, $farmer_name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        echo "Email sent successfully to $farmer_email!<br>";
    }
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>

