<?php
include 'db.php';

if (!isset($_POST['category']) || empty($_POST['category'])) {
    die("<p class='text-danger text-center'>No category selected!</p>");
}

$category = $_POST['category'];

// Fetch farmers who sell products in this category
$query = "SELECT DISTINCT u.id, u.name AS farmer_name, u.location, u.phone  
          FROM users u
          JOIN products p ON u.id = p.user_id
          WHERE p.category = ? AND u.role = 'farmer'";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$output = "<h3 class='text-center'>Farmers selling $category Products</h3><div class='row'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "
            <div class='col-md-4 mb-4'>
                <div class='card text-center shadow-sm'>
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($row['farmer_name']) . "</h5>
                        <p class='card-text'>📍 " . htmlspecialchars($row['location']) . "</p>
                        <p class='card-text'>📞 <strong>" . (!empty($row['phone']) ? htmlspecialchars($row['phone']) : 'Not Available') . "</strong></p>
                        <a href='farmer_profile.php?farmer_id=" . htmlspecialchars($row['id']) . "' class='btn btn-success'>View Products</a>
                    </div>
                </div>
            </div>
        ";
    }
} else {
    $output .= "<p class='text-center text-danger'>No farmers found in this category.</p>";
}

$output .= "</div>";

$stmt->close();
echo $output;
?>
