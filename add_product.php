<?php
session_start();
include 'db.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Error: User not logged in.");
    }

    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit']; // kg, g, l
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $stock = $_POST['stock']; // New stock input

    // Image Upload
    $target_dir = "uploads/";
    $image = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // Insert into database (Including user_id)
    $stmt = $conn->prepare("INSERT INTO products (name, quantity, unit, price, image, description, category, stock, user_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssii", $name, $quantity, $unit, $price, $image, $description, $category, $stock, $user_id);

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<html>
<title>AgroBazaar</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

<style>
    header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: green;
    color: white;
    padding: 10px 20px;
}
/* Form Container */
form {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

/* Input Fields */
form input,
form select,
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

/* File Input */
input[type="file"] {
    border: none;
    background: #fff;
}

/* Select Dropdown */
form select {
    cursor: pointer;
}

/* Submit Button */
form button {
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

form button:hover {
    background: #218838;
}

/* Form Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Responsive Design */
@media (max-width: 600px) {
    form {
        width: 90%;
    }
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
@media (max-width: 600px) {
    form {
        width: 90%;
    }
}
body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}
</style>
<header>
        <h1>AgroBazaar</h1>
       
        <a href="profile.php" style="display: inline-block; background-color:rgb(244, 20, 20); color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor='rgb(244, 20, 20)'; this.style.boxShadow='0px 0px 10pxrgb(244, 20, 20)'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' rgb(244, 20, 20)'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   Back to Home
</a>
    </header>
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
<form action="add_product.php" method="post" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    
    <input type="number" name="quantity" placeholder="Quantity" required>
    <select name="unit">
        <option value="kg">Kg</option>
        <option value="g">Gram</option>
        <option value="l">Litre</option>
    </select>

    <input type="number" name="stock" placeholder="Stock Available" required> <!-- New Field -->

    <input type="number" name="price" placeholder="Price in ₹" required>
    <input type="file" name="image" required>
    <textarea name="description" placeholder="Description"></textarea>

    <select name="category">
        <option value="Agriculture">Agriculture</option>
        <option value="Horticulture">Horticulture</option>
        <option value="Dairy">Dairy</option>
        <option value="Seeds">Seeds</option>
        <option value="Sericulture">Sericulture</option>
        <option value="Forestry">Forestry</option>
        <option value="Fishing">Fishery</option>
    </select>

    <button type="submit">Add Product</button>
</form>


