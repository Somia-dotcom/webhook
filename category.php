<?php
include 'db.php'; // Database connection

if (!isset($_GET['category'])) {
    echo "No category selected!";
    exit();
}

$category = $_GET['category']; // Get the selected category

// Fetch distinct farmers who sell products in this category
$query = "SELECT DISTINCT u.id, u.name, u.profile_image, u.location 
          FROM users u
          JOIN products p ON u.id = p.user_id
          WHERE p.category = ? AND u.role = 'farmer'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Farmers selling $category Products</h2>";

if ($result->num_rows > 0) {
    echo "<div class='farmer-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "
            <div class='farmer-card'>
                <img src='uploads/{$row['profile_image']}' alt='{$row['name']}' class='farmer-img'>
                <h3>{$row['name']}</h3>
                <p>📍 {$row['location']}</p>
                <a href='farmer_profile.php?farmer_id={$row['id']}' class='btn'>View Products</a>
            </div>
        ";
    }
    echo "</div>";
} else {
    echo "<p class='no-data'>No farmers available in this category.</p>";
}

$stmt->close();
?>
<style>
.farmer-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.farmer-card {
    background: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    width: 250px;
}
.farmer-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}
.farmer-card .btn {
    display: inline-block;
    background: green;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
}
.no-data {
    text-align: center;
    font-size: 18px;
    color: red;
}
</style>