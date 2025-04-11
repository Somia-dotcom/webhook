<?php
include 'db.php';

// Fetch all schemes from the database
$result = $conn->query("SELECT * FROM schemes ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Schemes - AgroBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- Bootstrap CDN -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <style>
        body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}
       
        .container {
            max-width: 900px;
            margin: auto;
            padding-top: 20px;
        }
        .scheme-card {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        .scheme-card h4 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .scheme-card p {
            color: #555;
        }
        .header {
            background: green;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 24px;
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
</head>
<body>

    <div class="header">
        Government Schemes
    </div>
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
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="scheme-card">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($row['details'])) ?></p>
                <p><a href="<?= htmlspecialchars($row['scheme_url']) ?>" target="_blank" class="btn btn-primary">View Scheme</a></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-danger">No schemes available at the moment.</p>
    <?php endif; ?>
</div>
</body>
</html>
