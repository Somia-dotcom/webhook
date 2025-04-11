<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in.");
}

$user_id = $_SESSION['user_id'];

$query = "SELECT o.id AS order_id, p.name AS product_name, o.quantity, o.total_price, pay.payment_status
          FROM orders o
          JOIN products p ON o.product_id = p.id
          LEFT JOIN payments pay ON o.id = pay.order_id
          WHERE o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Orders</h2>
<table border="1">
    <tr>
        <th>Order ID</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Payment Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₹<?= $row['total_price'] ?></td>
            <td><?= $row['payment_status'] ?></td>
        </tr>
    <?php } ?>
</table>
