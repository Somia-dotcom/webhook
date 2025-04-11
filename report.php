<?php
session_start();
include 'db.php';

// Get email input from form
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

// Determine report type
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'weekly';

switch ($filter) {
    case 'monthly':
        $interval = "INTERVAL 1 MONTH";
        break;
    case 'annual':
        $interval = "INTERVAL 1 YEAR";
        break;
    case 'weekly':
    default:
        $interval = "INTERVAL 1 WEEK";
        break;
}

$summaryData = [];
$chartData = [];
$product_names = [];
$sales_values = [];

if (!empty($email)) {
    // ✅ Summary Query (Farmer Info + Sales)
    $summaryQuery = "
        SELECT 
            u.id AS farmer_id,
            u.name AS farmer_name,
            u.email,
            SUM(p.amount) AS total_sales,
            COUNT(p.id) AS total_transactions,
            GROUP_CONCAT(DISTINCT pr.name SEPARATOR ', ') AS products_sold
        FROM payments p
        JOIN products pr ON p.product_id = pr.id
        JOIN users u ON pr.user_id = u.id
        WHERE p.status = 'Completed'
          AND u.email = ?
          AND p.payment_date >= NOW() - $interval
        GROUP BY u.id, u.name, u.email
    ";
    $summaryStmt = $conn->prepare($summaryQuery);
    $summaryStmt->bind_param("s", $email);
    $summaryStmt->execute();
    $summaryResult = $summaryStmt->get_result();
    $summaryData = $summaryResult->fetch_all(MYSQLI_ASSOC);

    // ✅ Bar Chart Query (All Products incl. ₹0 sales)
    $chartQuery = "
        SELECT 
            pr.name AS product_name,
            COALESCE(SUM(p.amount), 0) AS total_sales
        FROM users u
        JOIN products pr ON pr.user_id = u.id
        LEFT JOIN payments p ON p.product_id = pr.id 
            AND p.status = 'Completed'
            AND p.payment_date >= NOW() - $interval
        WHERE u.email = ?
        GROUP BY pr.name
        ORDER BY total_sales DESC
    ";
    $chartStmt = $conn->prepare($chartQuery);
    $chartStmt->bind_param("s", $email);
    $chartStmt->execute();
    $chartResult = $chartStmt->get_result();
    $chartData = $chartResult->fetch_all(MYSQLI_ASSOC);

    // Prepare for chart.js
    foreach ($chartData as $row) {
        $product_names[] = $row['product_name'];
        $sales_values[] = $row['total_sales'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    </style>
<body>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="farm_chat.php"><i class="fas fa-comments"></i> Farmchat</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="report.php">Report</a></li>
        <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
       
    </ul>

    
</nav>
<div class="container mt-5">
    <h2 class="mb-4">Farmer Sales Report</h2>

    <!-- Email input form -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="email" name="email" class="form-control" placeholder="Enter Farmer Email" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="col-md-6">
            <select name="filter" class="form-select">
                <option value="weekly" <?php if ($filter == 'weekly') echo 'selected'; ?>>Weekly</option>
                <option value="monthly" <?php if ($filter == 'monthly') echo 'selected'; ?>>Monthly</option>
                <option value="annual" <?php if ($filter == 'annual') echo 'selected'; ?>>Annual</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </div>
    </form>

    <?php if (!empty($email)): ?>
        <?php if (count($summaryData) > 0): ?>
            <!-- Summary Table -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Farmer ID</th>
                        <th>Farmer Name</th>
                        <th>Email</th>
                        <th>Products Sold</th>
                        <th>Total Transactions</th>
                        <th>Total Sales (₹)</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($summaryData as $row): ?>
                    <tr>
                        <td><?php echo $row['farmer_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['products_sold']); ?></td>
                        <td><?php echo $row['total_transactions']; ?></td>
                        <td>₹<?php echo number_format($row['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Bar Chart -->
            <?php if (count($chartData) > 0): ?>
                <div class="mt-5">
                    <h4>Sales Per Product (Bar Chart)</h4>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    const salesChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($product_names); ?>,
                            datasets: [{
                                label: 'Sales (₹)',
                                data: <?php echo json_encode($sales_values); ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Sales in ₹'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Product Name'
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-danger">No sales found for the entered email and selected period.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted">Enter a farmer's email above to view the report.</p>
    <?php endif; ?>
</div>
</body>
</html>
