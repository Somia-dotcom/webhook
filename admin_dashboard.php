<?php
session_start();
include 'db.php';



// Fetch counts for dashboard
$total_farmers = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role='farmer'")->fetch_assoc()['count'];
$total_buyers = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role='buyer'")->fetch_assoc()['count'];
$total_machines = $conn->query("SELECT COUNT(*) AS count FROM services WHERE service_type='Machine Rental'")->fetch_assoc()['count'];
$available_machines = $conn->query("SELECT COUNT(*) AS count FROM services WHERE service_type='Machine Rental' AND rental_category IS NULL")->fetch_assoc()['count'];
$rented_machines = $total_machines - $available_machines;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgroBazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<style>
    /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Admin Header */
.admin-header {
    background-color: #2c3e50;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-header h1 {
    margin: 0;
    font-size: 24px;
}

.admin-header .logout-btn {
    background-color: #e74c3c;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
}

.admin-header .logout-btn:hover {
    background-color: #c0392b;
}

/* Admin Dashboard Layout */
.admin-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.admin-sidebar {
    width: 250px;
    background-color: #34495e;
    color: white;
    padding: 20px;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.admin-sidebar ul {
    list-style: none;
    padding: 0;
}

.admin-sidebar ul li {
    padding: 15px;
    font-size: 16px;
}

.admin-sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    transition: background 0.3s;
}

.admin-sidebar ul li a:hover {
    background: #1abc9c;
    border-radius: 5px;
}

/* Main Content */
.admin-content {
    margin-left: 250px;
    padding: 20px;
    flex-grow: 1;
}

/* Dashboard Cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.dashboard-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.dashboard-card h3 {
    margin: 10px 0;
    font-size: 20px;
}

.dashboard-card p {
    font-size: 16px;
    color: #555;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 15px;
    text-decoration: none;
    color: white;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.3s;
}

.btn-primary {
    background: #3498db;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-danger {
    background: #e74c3c;
}

.btn-danger:hover {
    background: #c0392b;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }

    .admin-sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }

    .admin-content {
        margin-left: 0;
    }
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

<!-- Admin Header -->
<header class="admin-header">
    <h1>AgroBazaar Admin Panel</h1>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<div class="admin-container">

    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_farmers.php">Manage Farmers</a></li>
            <li><a href="manage_buyers.php">Manage Buyers</a></li>
            <li><a href="manage_rentals.php">Machine Rentals</a></li>
            <li><a href="update_schemes.php">Update Schemes</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <h2>Welcome, Admin</h2>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Farmers</h3>
                <p><?= $total_farmers ?> registered</p>
                <a href="manage_farmers.php" class="btn btn-primary">Manage</a>
            </div>

            <div class="dashboard-card">
                <h3>Buyers</h3>
                <p><?= $total_buyers ?> registered</p>
                <a href="manage_buyers.php" class="btn btn-primary">Manage</a>
            </div>

            <div class="dashboard-card">
                <h3>Total Machines</h3>
                <p><?= $total_machines ?> available</p>
            </div>

            <div class="dashboard-card">
                <h3>Machines Rented</h3>
                <p><?= $rented_machines ?> in use</p>
            </div>

            <div class="dashboard-card">
                <h3>Available Machines</h3>
                <p><?= $available_machines ?> available</p>
                <a href="manage_rentals.php" class="btn btn-primary">View Rentals</a>
            </div>
        </div>
    </main>

</div>

</body>
</html>
