<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
                <div class='col-md-4 mb-4'>
                    <div class='card'>
                        <img src='uploads/{$row['image']}' class='card-img-top' alt='{$row['name']}' style='height: 200px; object-fit: cover;'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['name']}</h5>
                            <p class='card-text'>Stock Available: {$row['stock']} {$row['unit']}</p>
                            <p class='card-text'>Price: ₹{$row['price']} per {$row['unit']}</p>
                            <p class='card-text'>{$row['description']}</p>
                            
                            <!-- Purchase Button -->
                            <form action='purchase.php' method='POST'>
                                <input type='hidden' name='product_id' value='{$row['id']}'>
                                <input type='number' name='purchase_quantity' placeholder='Enter quantity' required>
                                <button type='submit' class='purchase-btn'>Purchase</button>
                            </form>
                        </div>
                    </div>
                </div>
            ";
        }
    } else {
        echo "<p class='text-center text-danger'>No products available in this category.</p>";
    }
    $stmt->close();
}
?>
