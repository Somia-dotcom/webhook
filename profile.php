<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT name, role, profile_image FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_name = $user['name'] ?? "Guest";
    $user_role = $user['role'] ?? "User";
    $profile_image = !empty($user['profile_image']) ? "uploads/" . $user['profile_image'] : "default.png";
} else {
    $user_name = "Guest";
    $user_role = "User";
    $profile_image = "default.png";
}

// Handle product upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];

    $image_name = basename($_FILES["product_image"]["name"]);
    $target_file = "uploads/" . $image_name;
    
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        $query = "INSERT INTO products (farmer_id, name, price, category, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_category', '$image_name')";
        mysqli_query($conn, $query);
        $success = "Product added successfully!";
    } else {
        $error = "Error uploading product image.";
    }
}

// Handle promotion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_promotion'])) {
    $product_id = $_POST['product_id'];
    $promotion_text = $_POST['promotion_text'];

    $query = "INSERT INTO promotions (farmer_id, product_id, promotion_text) VALUES ('$user_id', '$product_id', '$promotion_text')";
    mysqli_query($conn, $query);
    $success = "Promotion added successfully!";}

// Handle service addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $service_name = $_POST['service_name'];
    $service_description = $_POST['service_description'];

    $query = "INSERT INTO services (id, name, description) VALUES ('$user_id', '$service_name', '$service_description')";
    mysqli_query($conn, $query);
    $success = "Service added successfully!";
}

// Fetch farmer's products
$products_query = "SELECT * FROM products WHERE id = '$user_id'";
$products_result = mysqli_query($conn, $products_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Profile</title>
    
</head>
<style>
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
.banner {
    width: 100%;
    overflow: hidden;
    position: relative;
}

.promo-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.promo-slider {
    display: flex;
    width: max-content;
    animation: slideBanner 12s infinite linear;
}

.promo-item {
    width: 300px; /* Adjust width as needed */
    margin: 10px;
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s;
}

.promo-item:hover {
    transform: scale(1.05);
}

.promo-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.promo-details {
    font-size: 16px;
    font-weight: bold;
    margin-top: 10px;
}

.promo-contact {
    font-size: 14px;
    color: #555;
    margin-top: 5px;
}

/* Auto-slide animation */
@keyframes slideBanner {
    0% { transform: translateX(0); }
    33% { transform: translateX(-100%); }
    66% { transform: translateX(-200%); }
    100% { transform: translateX(0); }
}
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    text-align: center;
}
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: green;
    color: white;
    padding: 10px 20px;
}
.profile {
    display: flex;
    align-items: center;
}
.profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}
.profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}
.logout-btn {
    margin-left: 10px;
    color: white;
    background: red;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
}
.product-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
}
.product {
    border: 1px solid #ccc;
    padding: 15px;
    margin: 10px;
    width: 250px;
    text-align: center;
    border-radius: 5px;
}
.product img {
    width: 100%;
    height: 150px;
}
.promoted {
    border: 2px solid gold;
    background: lightyellow;
}
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

.category-container {
    display: flex;
    justify-content: center;
    gap: 15px;
}
.category {
    padding: 10px;
    background: #f4a261;
    border-radius: 50%;
    width: 100px;
    height: 100px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
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

/* Form Container */
.form-container {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

/* Form Heading */
.form-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #28a745;
}

/* Input Fields */
.form-container input,
.form-container select,
.form-container textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

/* Focus Effect */
.form-container input:focus,
.form-container select:focus,
.form-container textarea:focus {
    border-color: #28a745;
    box-shadow: 0px 0px 5px rgba(40, 167, 69, 0.5);
    outline: none;
}

/* File Input */
.form-container input[type="file"] {
    border: none;
    background: #fff;
}

/* Submit Button */
.form-container button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.form-container button:hover {
    background: #218838;
}

/* Responsive Design */
@media (max-width: 600px) {
    .form-container {
        width: 90%;
    }
}

.profile-form {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.profile-form label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
    font-size: 14px;
}

.profile-form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.profile-form input[type="file"] {
    border: none;
}

.profile-form button {
    width: 100%;
    padding: 10px;
    margin-top: 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.profile-form button:hover {
    background: #218838;
}

/* Profile Image Preview */
.profile-image-container {
    text-align: center;
    margin-bottom: 10px;
}

.profile-image-container img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ccc;
}

body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}

    /* Profile Section */

form {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    margin: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

form input, form textarea, form select {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

form button {
    background: #4CAF50;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
}

form button:hover {
    background: #45a049;
}
.add-product-container {
    text-align: center;
    margin: 20px 0;
}

.add-product-icon {
    font-size: 40px;
    font-weight: bold;
    color: #4CAF50;
    cursor: pointer;
    display: inline-block;
    border: 2px solid #4CAF50;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    line-height: 46px;
    text-align: center;
    transition: 0.3s;
}

.add-product-icon:hover {
    background: #4CAF50;
    color: white;
}

/* Centering the Profile Section */
.profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 80vh;
}

/* Profile Section */
.profile-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 300px;
}

.profile-section img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 3px solid #4CAF50;
}

.profile-section a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

.profile-section a:hover {
    background: #45a049;
}
 </style>
<body>
    <header>
    <h1>AgroBazaar</h1>
        <div class="profile">
            <?php if (isset($_SESSION['user_id'])) { ?>
                
            <?php } else { ?>
                <a href="otp_login.php">Login</a> | <a href="register.php">Register</a>
            <?php } ?>
        </div>
        <div class="profile">
    <a href="profile.php">
        <img src="uploads/<?php echo $user_icon; ?>" class="profile-icon" title="View Profile">
    </a>
    <span><?php echo $user_name; ?> (<?php echo ucfirst($user_role); ?>)</span>
    <a href="otp_login.php" class="logout-btn">Logout</a>
</div>
</header>
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>

    <div class="profile-container">
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" width="100">
        <p>Role: <?php echo ucfirst(htmlspecialchars($user_role)); ?></p>
        <a href="profile_edit.php">Edit Profile</a>
        <br>
        
            <!-- Add Product Icon -->
<div class="add-product-container">
    <p>ADD PRODUCTS</p>
    <span class="add-product-icon" id="addProduct">+</span>
</div>
<div class="add-product-container">
    
    <p>ADD SERVICE</p><span class="add-product-icon" id="addservice">+</span>
</div>
<div class="add-product-container">
    <p>ADD PROMOTION</p>
    <span class="add-product-icon" id="addpromotion">+</span>
</div>
    </div>
</div>

<a href="index.php" style="display: inline-block; background-color:  #28a745; color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor=' #28a745'; this.style.boxShadow='0px 0px 10px  #28a745'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' #28a745'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   Back to Home
</a>

<br><br>
<a href="manage_product.php" style="display: inline-block; background-color:  #28a745; color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor=' #28a745'; this.style.boxShadow='0px 0px 10px  #28a745'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' #28a745'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   PRODUCT MANAGEMENT
</a>

   
    <script>
        $(document).ready(function() {
            $('#addProduct').click(function() {
                $('#productForm').slideToggle();
            });
            $('#addService').click(function() {
                $('#serviceForm').slideToggle();
            });
            $('#addPromotion').click(function() {
                $('#promotionForm').slideToggle();
            });
        });
    </script>
    <script>
    document.getElementById("addProduct").addEventListener("click", function() {
        window.location.href = "add_product.php";
    });
</script>
<script>
    document.getElementById("addservice").addEventListener("click", function() {
        window.location.href = "add_service.php";
    });
</script>
<script>
    document.getElementById("addpromotion").addEventListener("click", function() {
        window.location.href = "add_promotion.php";
    });
</script>


</body>
</html>
