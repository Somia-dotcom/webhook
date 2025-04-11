<?php
include 'db.php';
$result = $conn->query("SELECT DISTINCT category FROM products");

// Fetch latest text-based promotions from farmers
$promotions = $conn->query("SELECT * FROM promotions WHERE created_at >= NOW() - INTERVAL 48 HOUR ORDER BY created_at DESC LIMIT 5");

// Handle Promotion Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $farmer_name = $_POST['farmer_name'];
    $contact = $_POST['contact'];
    $image = NULL;
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Error uploading image.");
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO promotions (title, description, farmer_name, contact, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $farmer_name, $contact, $image);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        die("Database error: " . $stmt->error);
    }
    $stmt->close();
}
?>
<html>
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
    <link rel="stylesheet" href="style.css">
</head>
<style>
.banner {
    width: 100%;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #28a745;
    color: white;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}
.promo-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    text-align: center;
}
.promo-item {
    padding: 15px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    color: #333;
    font-size: 20px;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}
.promo-contact {
    font-size: 18px;
    color: #555;
    margin-top: 5px;
}
.promo-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
}
/* Banner Section */
.banner {
    width: 100%;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #28a745;
    color: white;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

/* Promotion Container */
.promo-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

/* Promotion Item */
.promo-item {
    background: rgba(255, 255, 255, 0.95);
    padding: 20px;
    border-radius: 10px;
    color: #333;
    font-size: 18px;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.promo-item:hover {
    transform: scale(1.02);
    box-shadow: 0px 6px 12px rgba(0,0,0,0.15);
}

/* Promotion Contact Details */
.promo-contact {
    font-size: 16px;
    color: #555;
    margin-top: 8px;
    font-weight: 500;
}

/* Promotion Image */
.promo-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 10px;
}

/* Form Styling */
form {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

form label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

form input,
form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 15px;
    transition: border 0.3s ease-in-out;
}

form input:focus,
form textarea:focus {
    border-color: #28a745;
    outline: none;
    box-shadow: 0px 0px 5px rgba(40, 167, 69, 0.5);
}

/* Button Styling */
form button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease-in-out;
}

form button:hover {
    background: #218838;
}

/* Footer */
footer {
    margin-top: 30px;
    padding: 15px;
    background: #2c3e50;
    color: white;
    font-size: 14px;
    text-align: center;
    border-top: 3px solid #218838;
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
body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: green;
    color: white;
    padding: 10px 20px;
}
</style>

<body>
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
<form action="" method="post" enctype="multipart/form-data">
<h2>Submit a Promotion</h2>
    <label>Title:</label>
    <input type="text" name="title" class="form-control" required><br>
    
    <label>Description:</label>
    <textarea name="description" class="form-control" required></textarea><br>
    
    <label>Farmer Name:</label>
    <input type="text" name="farmer_name" class="form-control" required><br>
    
    <label>Contact:</label>
    <input type="text" name="contact" class="form-control" required><br>
    
    <label>Promotion Image (Optional):</label>
    <input type="file" name="image" class="form-control" accept="image/*"><br>
    
    <button type="submit" class="btn btn-success w-100">Add Promotion</button>
</form>

<header id="header">
    <div class="banner">
        <div class="promo-container">
            <?php while ($row = $promotions->fetch_assoc()) { ?>
                <div class="promo-item">
                    <?php if (!empty($row['image'])) { ?>
                        <div class="promo-image">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="Promotion Image">
                        </div>
                    <?php } ?>
                    "<?php echo $row['title']; ?> - <?php echo $row['description']; ?>"
                    <div class="promo-contact">Contact: <?php echo $row['farmer_name']; ?> | <?php echo $row['contact']; ?></div>
                </div>
            <?php } ?>
        </div>
    </div>
</header>

<main id="main-site"> ... </main>

                    </body>
                    </html>

