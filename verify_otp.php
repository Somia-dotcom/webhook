<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    // Check if OTP is correct
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $entered_otp) {
        $email = $_SESSION['otp_email'];

        // Fetch user details
        $query = "SELECT id, name, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Store user session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'buyer') {
                header("Location: buyer_dashboard.php");
            } else {
                header("Location: index.php"); // Redirect farmers
            }
            exit();
        } else {
            $error = "User not found!";
        }
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
</head>
<style>
    body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center; /* Centers horizontally */
    align-items: center; /* Centers vertically */
    height: 100vh; /* Full screen height */
    margin: 0;
}

form {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 350px;
    text-align: center;
    animation: fadeIn 0.8s ease-in-out;
}

h2 {
    margin-bottom: 15px;
    color: #2c3e50;
}

label {
    font-size: 16px;
    font-weight: bold;
    display: block;
    margin-bottom: 8px;
    color: #333;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 15px;
    text-align: center;
    transition: 0.3s ease-in-out;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0px 0px 8px rgba(52, 152, 219, 0.5);
}

button {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #218838;
}

/* Fade-in Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
    </style>
<body>

<div class="otp-container">
    <h2>Verify OTP</h2>
    
    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

    <form method="POST">
        <label>Enter OTP:</label>
        <input type="text" name="otp" required placeholder="Enter 6-digit OTP">
        <button type="submit">Verify OTP</button>
    </form>
</div>

</body>
</html>

