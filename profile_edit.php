<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details (including role)
$query = "SELECT name, email, address, location, area, products, profile_image, role, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error in query preparation: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'] ?? "Guest";
    $user_role = !empty($user['role']) ? ucfirst($user['role']) : "User"; // Default role
} else {
    die("User not found.");
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $location = trim($_POST['location']);
    $area = trim($_POST['area']);
    $products = trim($_POST['products']);
    $phone = trim($_POST['phone_no']); // Fix the form field name

    // Image upload handling
    $image_name = $user['profile_image']; // Keep existing image if no new file is uploaded
    if (!empty($_FILES["profile_image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]); // Unique file name
        $target_file = $target_dir . $image_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            die("Error uploading file!");
        }
    }

    // Update the user details (NOW INCLUDING PHONE)
    $update_query = "UPDATE users SET name = ?, email = ?, address = ?, location = ?, area = ?, products = ?, phone = ?, profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        die("Error in query preparation: " . $conn->error);
    }

    $stmt->bind_param("ssssssssi", $name, $email, $address, $location, $area, $products, $phone, $image_name, $user_id);
    if ($stmt->execute()) {
        $_SESSION['profile_image'] = $image_name;
        header("Location: profile.php?update=success");
        exit();
    } else {
        die("Error updating profile: " . $stmt->error);
    }
}

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="farm_chat.php">Farm Chat</a></li>
        <li><a href="report.php">Report</a></li>
        
    </ul>
    
</nav>
    

   
    <div class="container">
        <h2>Edit Profile</h2>

        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="profile-image-container">
                <img id="profilePreview" src="uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image">
            </div>

            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>


            <label>Phone:</label>
            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>


            <label>Location:</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" required>

            <label>Area:</label>
            <input type="text" name="area" value="<?php echo htmlspecialchars($user['area'] ?? ''); ?>" required>

            <label>Change Profile Image:</label>
            <input type="file" name="profile_image" id="profileImage">

            <button type="submit">Update Profile</button>
        </form>
        <a href="profile.php" style="display: inline-block; background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor=' #28a745'; this.style.boxShadow='0px 0px 10px #28a745'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' #28a745'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   Back to Home
</a>

        
    </div>


    <script>
        // Live preview of profile image
        document.getElementById("profileImage").addEventListener("change", function (event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById("profilePreview").src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });

        
    </script>
</body>
</html>
