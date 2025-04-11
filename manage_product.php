<?php
session_start();
include 'db.php'; 

// Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);

    $update_sql = "UPDATE products SET 
        name='$name', quantity='$quantity', unit='$unit', stock='$stock', 
        price='$price', description='$description', category='$category'
        WHERE id=$id";

    if ($conn->query($update_sql)) {
        $message = "Product updated successfully!";
    } else {
        $message = "Error updating product: " . $conn->error;
    }
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM products WHERE id=$id";
    
    if ($conn->query($delete_sql)) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error deleting product: " . $conn->error;
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products");

// Fetch product details for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f4f4f4;
    }
    h2 { text-align: center; }
    .container { background: white; padding: 20px; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; }
    table, th, td { border: 1px solid #ddd; padding: 10px; }
    th { background-color: #007bff; color: white; }
    .btn { padding: 5px 10px; text-decoration: none; }
    .btn-danger { background: red; color: white; }
    .btn-warning { background: orange; color: white; }
    
</style>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <?php if (isset($message)) { echo "<p class='alert alert-info'>$message</p>"; } ?>

    <!-- Product Update Form -->
    <?php if ($edit_product) { ?>
        <h3>Edit Product</h3>
        <form method="post">
            <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
            Name: <input type="text" name="name" value="<?= $edit_product['name'] ?>" required><br>
            Quantity: <input type="number" name="quantity" value="<?= $edit_product['quantity'] ?>" required><br>
            Unit: 
            <select name="unit">
                <option value="kg" <?= $edit_product['unit'] == 'kg' ? 'selected' : '' ?>>kg</option>
                <option value="g" <?= $edit_product['unit'] == 'g' ? 'selected' : '' ?>>g</option>
                <option value="l" <?= $edit_product['unit'] == 'l' ? 'selected' : '' ?>>l</option>
            </select><br>
            Stock: <input type="number" name="stock" value="<?= $edit_product['stock'] ?>" required><br>
            Price: <input type="text" name="price" value="<?= $edit_product['price'] ?>" required><br>
            Description: <textarea name="description"><?= $edit_product['description'] ?></textarea><br>
            Category: <input type="text" name="category" value="<?= $edit_product['category'] ?>" required><br>
            <button type="submit" name="update" class="btn btn-primary">Update Product</button>
        </form>
    <?php } ?>

    <!-- Product List -->
    <h3>Product List</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Stock</th>
            <th>Price (₹)</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td><?= $row['stock'] ?></td>
                <td>₹<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['category'] ?></td>
                <td>
                    <a href="manage_product.php?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                    <a href="add_product.php?delete=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>