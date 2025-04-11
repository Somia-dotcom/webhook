<?php
session_start();
require 'db.php';
require 'send_otp.php'; // File to send OTP via email

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        // Send OTP to the user's email
        sendOTP($email, $otp);

        // Redirect to OTP verification page
        header("Location: verify_otp.php");
        exit();
    } else {
        $error = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login with OTP</title>
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
/* OTP Container */
 /*  Form Container */
 form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        /*  Title */
        form h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /*  Input Fields */
       form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }

        /*  Input Focus Effect */
       form input:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
            outline: none;
        }

        /*  Button */
        form button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        form button:hover {
            background: #218838;
        }

        /*  Register Link */
        form p {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

       form a {
            color: #3498db;
            text-decoration: none;
        }

        form a:hover {
            text-decoration: underline;
        }

        /* 🌟 Fade-in Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
</style>
<body>
    
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
    <h2>Login with OTP</h2>
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Send OTP</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
</body>
</html>
