<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'admin') { 
                header("Location: admin_dashboard.php"); // Redirect to Admin Dashboard
            } elseif ($user['role'] == 'farmer') { 
                header("Location: index.php"); // Redirect to Farmers Dashboard
            } else { 
                header("Location: buyer_dashboard.php"); // Redirect to Buyer Dashboard
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
   /* General Styles */
   body {
    background: url('uploads/detail-rice-plant-sunset-valencia-with-plantation-out-focus-rice-grains-plant-seed (1).jpg') no-repeat center center/cover;
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
}
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Login Container */
form {
    max-width: 400px;
    width: 90%;
    background: white;
    padding: 25px;  /* Increased padding for better spacing */
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Heading */
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px; /* Added space below the heading */
}

/* Input Fields */
input {
    width: 100%;
    padding: 12px; /* Increased padding for better spacing */
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
    box-sizing: border-box; /* Prevents overflowing issues */
}

/* Focus Effect */
input:focus {
    border-color: #28a745;
    box-shadow: 0px 0px 5px rgba(40, 167, 69, 0.5);
    outline: none;
}

/* Submit Button */
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
    margin-top: 15px; /* Space between inputs and button */
}

button:hover {
    background: #218838;
}

/* Register Link */
p {
    text-align: center;
    margin-top: 15px; /* Increased margin for better spacing */
}

p a {
    color: #28a745;
    text-decoration: none;
    font-weight: bold;
}

p a:hover {
    text-decoration: underline;
}

/* Error Message */
p[style="color:red;"] {
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}

    </style>
<body>
    <form id="loginForm" method="POST">
        


    <h2>Login</h2>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>

   
    
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
