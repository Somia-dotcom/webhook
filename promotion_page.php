<?php
include 'db.php';
$result = $conn->query("SELECT DISTINCT category FROM products");

// Fetch latest text-based promotions from farmers
$promotions = $conn->query("SELECT * FROM promotions ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBazaar</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}
        /* Banner Styles */
        .banner {
            width: 100%;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #28a745;
            color: white;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .promo-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            text-align: center;
        }
        .promo-item {
            padding: 15px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        .promo-contact {
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <header id="header">
        <div class="banner">
            <div class="promo-container">
                <?php while ($row = $promotions->fetch_assoc()) { ?>
                    <div class="promo-item">"<?php echo $row['title']; ?> - <?php echo $row['description']; ?>"
                        <div class="promo-contact">Contact: <?php echo $row['farmer_name']; ?> | <?php echo $row['contact']; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </header>


    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 AgroBazaar. All Rights Reserved.</p>
    </footer>
</body>
</html>
