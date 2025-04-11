<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to make a purchase.");
}

// Get product ID from URL
if (!isset($_GET['product_id'])) {
    die("Invalid request.");
}
$product_id = intval($_GET['product_id']);

// Fetch product and farmer details
$query = "SELECT p.*, u.name AS farmer_name, u.phone 
          FROM products p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Product</title>
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
body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
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
    <h2>Confirm Your Purchase</h2>
    <div class="product-details">
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <p><strong>Price per <?php echo htmlspecialchars($product['unit']); ?>:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
        <p><strong>Stock Available:</strong> <?php echo htmlspecialchars($product['stock']); ?> <?php echo htmlspecialchars($product['unit']); ?></p>
        <p><strong>Farmer:</strong> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($product['phone']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
    </div>

    <form action="pay.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <label for="quantity">Enter Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['stock']; ?>" required>
        <p><strong>Total Price: ₹<span id="total_price">0</span></strong></p>
        <button type="submit">Confirm Purchase</button>
    </form>

    <script>
        document.getElementById("quantity").addEventListener("input", function() {
            let price = <?php echo $product['price']; ?>;
            let quantity = this.value;
            document.getElementById("total_price").innerText = price * quantity;
        });
    </script>
</body>
</html>



</body>
</html>
