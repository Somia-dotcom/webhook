<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "SELECT c.id AS cart_id, c.quantity, p.name, p.price, p.image, p.id AS product_id 
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- Bootstrap CDN -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
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
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="farm_chat.php">Farm Chat</a></li>
        <li><a href="report.php">Report</a></li>
        <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
    </ul>
    
</nav>
    <div class="container mt-5">
        <h2 class="text-center">Your Cart</h2>
        <?php if ($result->num_rows > 0) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { 
                        $item_total = $row['price'] * $row['quantity'];
                        $total_price += $item_total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><img src="uploads/<?= htmlspecialchars($row['image']); ?>" width="50" height="50"></td>
                            <td>₹<?= number_format($row['price'], 2); ?></td>
                            <td><?= $row['quantity']; ?></td>
                            <td>₹<?= number_format($item_total, 2); ?></td>
                            <td>
                            <a href="purchase.php?product_id=<?= htmlspecialchars($row['product_id'] ?? ''); ?>&quantity=<?= htmlspecialchars($row['quantity'] ?? ''); ?>" class="btn btn-success">Buy Now</a>

<a href="#" class="btn btn-danger remove-item" data-cart-id="<?= htmlspecialchars($row['id'] ?? ''); ?>">Remove</a>

 </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h4>Total Price: ₹<?= number_format($total_price, 2); ?></h4>

            
           <!-- Buy All Now Button -->
<form action="purchase.php" method="POST">
    <input type="hidden" name="buy_all" value="1">
    <button type="submit" class="btn btn-primary">Buy All</button>
                    </form>


        <?php } else { ?>
            <p class="text-center text-danger">Your cart is empty.</p>
        <?php } ?>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".remove-item").click(function (e) {
        e.preventDefault(); // Prevent default link behavior

        let cartId = $(this).data("cart-id"); // Get cart item ID
        let button = $(this); // Store reference to the button
        
        $.ajax({
            url: "remove_from_cart.php",
            type: "POST",
            data: { cart_id: cartId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    button.closest("tr").fadeOut(300, function () { $(this).remove(); }); // Remove row smoothly
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("Error: Could not remove item.");
            }
        });
    });
});
</script>


