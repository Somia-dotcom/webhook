<?php
include 'db.php';
session_start();



// Handle Buyer Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id = $delete_id AND role='buyer'");
    header("Location: manage_buyers.php?message=Buyer Removed Successfully");
    exit();
}

// Fetch Buyers
$result = $conn->query("SELECT id, name, email, phone FROM users WHERE role='buyer'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Buyers - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Buyers</h2>
        <?php if (isset($_GET['message'])) echo "<p class='text-success'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td>
                            <a href="manage_buyers.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this buyer?');">Remove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
