<?php
session_start();
include 'db.php';




// Fetch all farmers from the database
$query = "SELECT id, name, email, phone, location FROM users WHERE role = 'farmer'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Farmers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Manage Farmers</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        
        <!-- Add Farmer Button -->
        <button class="btn btn-primary mb-3" onclick="document.getElementById('addFarmerModal').style.display='block'">Add Farmer</button>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                    <td><?= htmlspecialchars($row['location']); ?></td>
                    <td>
                        <a href="edit_farmer.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_farmer.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        <a href="manage_product.php" style="display: inline-block; background-color:  #28a745; color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor=' #28a745'; this.style.boxShadow='0px 0px 10px  #28a745'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' #28a745'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   PRODUCT MANAGEMENT
</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add Farmer Modal -->
    <div id="addFarmerModal" class="modal" style="display:none;">
        <div class="modal-content p-4">
            <h3>Add New Farmer</h3>
            <form action="add_farmer.php" method="POST">
                <input type="text" name="name" placeholder="Farmer Name" class="form-control mb-2" required>
                <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                <input type="text" name="phone" placeholder="Phone" class="form-control mb-2" required>
                <input type="text" name="location" placeholder="Location" class="form-control mb-2" required>
                <button type="submit" class="btn btn-success">Add Farmer</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addFarmerModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            let modal = document.getElementById('addFarmerModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
