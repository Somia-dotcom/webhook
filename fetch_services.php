<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['service_type'])) {
        die("Error: No service type received.");
    }

    $service_type = $_POST['service_type'];

    // Fetch services based on selected service type
    $stmt = $conn->prepare("SELECT * FROM services WHERE service_type = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("s", $service_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure correct path for images
            $image_src = (!empty($row['image'])) ? "uploads/" . $row['image'] : "default.jpg";

            echo "
                <div class='col-md-4 mb-4'>
                    <div class='card'>
                        <img src='{$image_src}' class='card-img-top' alt='{$row['name']}' style='height: 200px; object-fit: cover;'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['name']}</h5>
                            <p class='card-text'><strong>Rental Category: </strong>" . ($row['rental_category'] ?? 'N/A') . "</p>
                            <p class='card-text'><strong>Machinery Selected: </strong>" . ($row['machinery_selected'] ?? 'N/A') . "</p>
                            <p class='card-text'><strong>Price: </strong>₹{$row['price']}</p>
                            <p class='card-text'>{$row['description']}</p>
                            <p class='card-text'><strong>Contact: </strong>{$row['contact']}</p>
                        </div>
                    </div>
                </div>
            ";
        }
    } else {
        echo "<p class='text-center text-danger'>No services available in this category.</p>";
    }
    $stmt->close();
}
?>
