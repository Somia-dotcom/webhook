<?php
include 'db.php';
session_start();

// Handle Adding a New Scheme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_scheme'])) {
    $title = $_POST['title'];
    $details = $_POST['details'];
    $scheme_url = $_POST['scheme_url'];

    $stmt = $conn->prepare("INSERT INTO schemes (title, details, scheme_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $details, $scheme_url);
    if ($stmt->execute()) {
        header("Location: update_schemes.php?message=Scheme Added Successfully");
        exit();
    } else {
        $error = "Failed to add scheme!";
    }
}

// Handle Editing an Existing Scheme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_scheme'])) {
    $scheme_id = intval($_POST['scheme_id']);
    $title = $_POST['title'];
    $details = $_POST['details'];
    $scheme_url = $_POST['scheme_url'];

    $stmt = $conn->prepare("UPDATE schemes SET title = ?, details = ?, scheme_url = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $details, $scheme_url, $scheme_id);
    if ($stmt->execute()) {
        header("Location: update_schemes.php?message=Scheme Updated Successfully");
        exit();
    } else {
        $error = "Failed to update scheme!";
    }
}

// Fetch All Schemes
$result = $conn->query("SELECT * FROM schemes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Schemes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; margin: auto; padding-top: 20px; }
        .form-container { background: #f8f9fa; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .table-container { margin-top: 20px; }
        .btn { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Manage Government Schemes</h2>
        <?php if (isset($_GET['message'])) echo "<p class='text-success'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
        
        <!-- Add New Scheme Form -->
        <div class="form-container">
            <h4>Add New Scheme</h4>
            <form method="POST">
                <input type="text" name="title" placeholder="Scheme Title" class="form-control mb-2" required>
                <textarea name="details" placeholder="Scheme Details" class="form-control mb-2" required></textarea>
                <input type="url" name="scheme_url" placeholder="Scheme URL" class="form-control mb-2" required>
                <button type="submit" name="add_scheme" class="btn btn-success w-100">Add Scheme</button>
            </form>
        </div>

        <!-- Existing Schemes Table -->
        <div class="table-container">
            <h4>Existing Schemes</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Details</th>
                        <th>Scheme URL</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['details']) ?></td>
                            <td><a href="<?= htmlspecialchars($row['scheme_url']) ?>" target="_blank">View Scheme</a></td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn" 
                                    data-id="<?= $row['id'] ?>" 
                                    data-title="<?= htmlspecialchars($row['title']) ?>" 
                                    data-details="<?= htmlspecialchars($row['details']) ?>"
                                    data-url="<?= htmlspecialchars($row['scheme_url']) ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Scheme Modal -->
        <div id="editModal" style="display:none;" class="form-container">
            <h4>Edit Scheme</h4>
            <form method="POST">
                <input type="hidden" name="scheme_id" id="schemeId">
                <input type="text" name="title" id="schemeTitle" class="form-control mb-2" required>
                <textarea name="details" id="schemeDetails" class="form-control mb-2" required></textarea>
                <input type="url" name="scheme_url" id="schemeUrl" class="form-control mb-2" required>
                <button type="submit" name="update_scheme" class="btn btn-success w-100">Update Scheme</button>
            </form>
        </div>
    </div>

    <script>
        // Open Modal with Scheme Data
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('schemeId').value = this.dataset.id;
                document.getElementById('schemeTitle').value = this.dataset.title;
                document.getElementById('schemeDetails').value = this.dataset.details;
                document.getElementById('schemeUrl').value = this.dataset.url;
                document.getElementById('editModal').style.display = 'block';
            });
        });
    </script>
</body>
</html>
