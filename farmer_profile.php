<?php
include 'db.php';

if (!isset($_GET['farmer_id']) || empty($_GET['farmer_id'])) {
    die("<h3 class='text-danger text-center'>Invalid Request!</h3>");
}

$farmer_id = intval($_GET['farmer_id']);

// Fetch farmer details
$stmt = $conn->prepare("SELECT name, profile_image, location, email FROM users WHERE id = ? AND role = 'farmer'");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$farmer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$farmer) {
    die("<h3 class='text-danger text-center'>Farmer Not Found!</h3>");
}

// Fetch products of this farmer
$stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ?");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($farmer['name']); ?> - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- Bootstrap CDN -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
</head>
<style>
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
</style>
<body class="bg-light">

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

    <div class="container mt-5">
        <div class="text-center">
            <img src="uploads/<?= htmlspecialchars($farmer['profile_image']); ?>" alt="Farmer Image" class="rounded-circle" width="120">
            <h2><?= htmlspecialchars($farmer['name']); ?></h2>
            <p>📍 <?= htmlspecialchars($farmer['location']); ?></p>
            <p>📧 <a href="mailto:<?= htmlspecialchars($farmer['email']); ?>"><?= htmlspecialchars($farmer['email']); ?></a></p>
        </div>

        <h4 class="mt-4">Products Sold by <?= htmlspecialchars($farmer['name']); ?></h4>
        <div class="row">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="uploads/<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text">Price: ₹<?= htmlspecialchars($row['price']); ?> per <?= htmlspecialchars($row['unit']); ?></p>
                                <p class="card-text">Stock: <?= htmlspecialchars($row['stock']); ?> <?= htmlspecialchars($row['unit']); ?></p>
                                <a href="purchase.php?product_id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-success">Buy Now</a>
                                <button class="btn btn-warning add-to-cart" data-product="<?= $row['id'] ?>">Add to Cart</button>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-danger">No products available from this farmer.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
        let product_id = this.getAttribute('data-product');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "add_to_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText);
            }
        };

        xhr.send("product_id=" + encodeURIComponent(product_id));
    });
});
</script>
</body>
</html>



<?php
$stmt->close();
?>
