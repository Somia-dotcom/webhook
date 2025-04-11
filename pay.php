<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to make a purchase.");
}

$user_id = $_SESSION['user_id'];

// Validate product ID and quantity
if (!isset($_POST['product_id'], $_POST['quantity'])) {
    die("Invalid request.");
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

if ($quantity <= 0) {
    die("Invalid quantity selected.");
}

// Fetch product details
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// Ensure requested quantity does not exceed available stock
if ($quantity > $product['stock']) {
    die("Insufficient stock available.");
}

// Calculate total price
$total_price = $quantity * $product['price'];

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    // Insert order into orders table (without product_id and quantity)
    $order_query = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("id", $user_id, $total_price);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id; // Get last inserted order ID

    // Insert into order_items table (to track products & quantities)
    $order_items_query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $order_items_stmt = $conn->prepare($order_items_query);
    $order_items_stmt->bind_param("iii", $order_id, $product_id, $quantity);
    $order_items_stmt->execute();

    // Insert payment details with product_id
$payment_query = "INSERT INTO payments (order_id, user_id, product_id, amount, payment_method, status) VALUES (?, ?, ?, ?, ?, 'Completed')";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->bind_param("iiids", $order_id, $user_id, $product_id, $total_price, $payment_method);
$payment_stmt->execute();

    // Deduct purchased quantity from stock
    $update_stock_query = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $update_stock_stmt = $conn->prepare($update_stock_query);
    $update_stock_stmt->bind_param("ii", $quantity, $product_id);
    $update_stock_stmt->execute();

    // Redirect to invoice page
    header("Location: invoice.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
</head>
<style>
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

/* Responsive Design */
@media (max-width: 600px) {
    .product-details,
    form {
        width: 90%;
    }
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
    <div class="container">
        <h2 class="mt-4">Complete Your Payment</h2>
        <h3>Product: <?php echo htmlspecialchars($product['name']); ?></h3>
        <p><strong>Price per <?php echo htmlspecialchars($product['unit']); ?>:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
        <h3><strong>Total Amount: ₹<?php echo number_format($total_price, 2); ?></strong></h3>

        <form method="POST" action="" id="payment-form">

            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">

            <label>Select Payment Method:</label><br>
            <input type="radio" name="payment_method" value="Credit Card" required> Credit/Debit Card<br>
            <input type="radio" name="payment_method" value="UPI"> UPI<br>
            <input type="radio" name="payment_method" value="Cash on Delivery"> Cash on Delivery<br><br>
            <div id="error-message" class="text-danger mb-2"></div>

            <button type="submit" class="btn btn-success">Confirm & Pay</button>
            <div id="upi-scanner" style="display:none; margin-top: 20px;">
    <p><strong>Scan this UPI code to pay:</strong></p>
    <img src="uploads/scaner.jpg" alt="UPI QR Code" style="width: 250px; height: auto; border: 1px solid #ccc; padding: 10px;">
</div>

<div id="credit-card-form" style="display:none; margin-top: 20px;">
    <div class="mb-3">
        <label for="card_number" class="form-label">Card Number</label>
        <input type="text" class="form-control" name="card_number" id="card_number" placeholder="XXXX-XXXX-XXXX-XXXX">
    </div>
    <div class="mb-3">
        <label for="expiry_date" class="form-label">Expiry Date</label>
        <input type="text" class="form-control" name="expiry_date" id="expiry_date" placeholder="MM/YY">
    </div>
    <div class="mb-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="password" class="form-control" name="cvv" id="cvv" placeholder="CVV">
    </div>
</div>

        </form>
    </div>
    <script>
    // When the DOM is fully loaded
    document.addEventListener("DOMContentLoaded", function () {
        const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
        const upiScanner = document.getElementById("upi-scanner");

        paymentOptions.forEach(option => {
            option.addEventListener('change', function () {
                if (this.value === "UPI") {
                    upiScanner.style.display = "block";
                } else {
                    upiScanner.style.display = "none";
                }
            });
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
        const upiScanner = document.getElementById("upi-scanner");
        const creditCardForm = document.getElementById("credit-card-form");
        const form = document.getElementById("payment-form");
        const errorDiv = document.getElementById("error-message");

        paymentOptions.forEach(option => {
            option.addEventListener('change', function () {
                errorDiv.innerText = ""; // Clear error on change
                if (this.value === "UPI") {
                    upiScanner.style.display = "block";
                    creditCardForm.style.display = "none";
                } else if (this.value === "Credit Card") {
                    creditCardForm.style.display = "block";
                    upiScanner.style.display = "none";
                } else {
                    upiScanner.style.display = "none";
                    creditCardForm.style.display = "none";
                }
            });
        });

        form.addEventListener("submit", function (e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedMethod) return;

            if (selectedMethod.value === "Credit Card") {
                const cardNumber = document.getElementById("card_number").value.trim();
                const expiryDate = document.getElementById("expiry_date").value.trim();
                const cvv = document.getElementById("cvv").value.trim();

                // Validate card number (12 or more digits)
                const digitsOnly = cardNumber.replace(/\D/g, "");
                if (digitsOnly.length < 12) {
                    errorDiv.innerText = "Card number must be at least 12 digits.";
                    e.preventDefault();
                    return;
                }

                // Validate expiry date format MM/YY and future date
                const match = expiryDate.match(/^(\d{2})\/(\d{2})$/);
                if (!match) {
                    errorDiv.innerText = "Expiry date must be in MM/YY format.";
                    e.preventDefault();
                    return;
                }

                const month = parseInt(match[1], 10);
                const year = parseInt("20" + match[2], 10); // assuming 20YY

                const today = new Date();
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth() + 1;

                if (month < 1 || month > 12 || (year < currentYear) || (year === currentYear && month < currentMonth)) {
                    errorDiv.innerText = "Expiry date is invalid or already passed.";
                    e.preventDefault();
                    return;
                }

                // Validate CVV (6 digits)
                if (!/^\d{6}$/.test(cvv)) {
                    errorDiv.innerText = "CVV must be exactly 6 digits.";
                    e.preventDefault();
                    return;
                }

                // Clear error if all good
                errorDiv.innerText = "";
            }
        });
    });
</script>


</body>
</html>
