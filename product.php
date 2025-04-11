<?php
include 'db.php';
$result = $conn->query("SELECT DISTINCT category FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBazaar - Farmers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/style.css">
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
        
        
</div>
    </header>
    <header id="header"></header>
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

    <main id="main-site">
        <!-- Categories Section -->
        <section id="categories" class="container py-5">
            <h4 class="text-center">Product Categories</h4>
            <div class="d-flex justify-content-center gap-3">
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Agriculture')">Agriculture</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Horticulture')">Horticulture</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Dairy')">Dairy</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Seeds')">Seeds</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Sericulture')">Sericulture</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Forestry')">Forestry</button>
                <button class="btn btn-success rounded-circle p-4" onclick="filterCategory('Fishing')">Fishery</button>
            </div>
        </section>

        <!-- Farmers Section -->
        <section id="farmers-list" class="container py-5">
            <h4 class="text-center"> <span id="category-name"></span> </h4>
            <div class="row" id="farmers-container"></div>
        </section>
    </main>

   
    <script>
    function filterCategory(category) {
        document.getElementById("category-name").innerText = category;

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "fetch_farmers.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById("farmers-container").innerHTML = xhr.responseText;
            }
        };

        xhr.send("category=" + encodeURIComponent(category));
    }
    </script>
</body>
</html>
