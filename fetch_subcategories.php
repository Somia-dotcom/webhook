<?php
require 'db.php';

if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $query = "SELECT * FROM subcategories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<option value=''>Select Subcategory</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['subcategory_name']}</option>";
    }
}
?>
