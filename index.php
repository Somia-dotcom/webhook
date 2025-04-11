<?php
session_start();
require 'db.php';
$promotions = $conn->query("SELECT * FROM promotions ORDER BY created_at DESC LIMIT 5");

if (!$promotions) {
    die("Error fetching promotions: " . $conn->error);
}

// Check if user is logged in
$user_icon = "default.png"; // Default icon
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $user_role = $_SESSION['user_role'];

    // Fetch user profile picture (if available)
    $user_query = "SELECT profile_image FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_query);
    if ($row = mysqli_fetch_assoc($user_result)) {
        $user_icon = $row['profile_image'] ?: "default.png"; // Use profile image if available
    }
}

// Fetch promoted products (Featured Products)
$promoted_query = "SELECT * FROM products WHERE is_promoted = 1 ORDER BY created_at DESC LIMIT 5";


// Fetch all products
$products_query = "SELECT * FROM products ORDER BY created_at DESC";


$query = "SELECT name, role FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_name = $user['name'] ?? "Guest";  // Default to 'Guest' if null
    $user_role = $user['role'] ?? "User";   // Default to 'User' if null
} else {
    $user_name = "Guest";  // Provide default values if no user is found
    $user_role = "User";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBazaar - Home</title>
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    
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

</style>

<body>
    <header>
        <h1>AgroBazaar</h1>
        <div class="profile">
            <?php if (isset($_SESSION['user_id'])) { ?>
                
            <?php } else { ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
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
        <li><a href="farm_chat.php"><i class="fas fa-comments"></i> Farmchat</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="report.php">Report</a></li>
        <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
       
    </ul>

    
</nav>

<div id="farmer-list"></div>

<script src="script.js"></script>
    
    </section>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/agro_icon.png" type="image/x-icon">
    <title>AgroBazaar</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <header id="header">
       
    </header>

    <main id="main-site">
    <div class="banner">
    <div class="promo-container">
        <div class="promo-slider">
            <?php while ($row = $promotions->fetch_assoc()) { ?>
                <div class="promo-item">
                    <?php if (!empty($row['image'])) { ?>
                        <div class="promo-image">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="Promotion Image">
                        </div>
                    <?php } ?>
                    <div class="promo-details">
                        <strong><?php echo $row['title']; ?></strong> - <?php echo $row['description']; ?>
                    </div>
                    <div class="promo-contact">
                        Contact: <?php echo $row['farmer_name']; ?> | <?php echo $row['contact']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
   
    </main>
    <br>
    <br>

   

    <script src="./js/script.js"></script>
    <script>
    $(document).ready(function(){
    setInterval(function(){
        $('.promo-slider').animate({marginLeft: '-=320px'}, 1000, function(){
            $(this).append($(this).children().first()); 
            $(this).css('marginLeft', '0');
        });
    }, 3000);
});
</script>


 
</body>

</html>
