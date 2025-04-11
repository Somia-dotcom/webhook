<?php
include 'db.php';
session_start();

// Ensure only admin can access


// Fetch Machine Rentals
$query = "SELECT service_id, name, rental_category, price, contact, (CASE WHEN rental_category IS NULL THEN 'Available' ELSE 'Rented' END) AS status FROM services WHERE service_type='Machine Rental'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rentals - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Machine Rentals</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Category</th>
                    <th>Price (₹)</th>
                    <th>Contact</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['rental_category'] ?: 'N/A') ?></td>
                        <td>₹<?= htmlspecialchars($row['price']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td class="<?= $row['status'] === 'Available' ? 'text-success' : 'text-danger' ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
