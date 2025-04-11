<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        $error = "Email already registered!";
    } else {
        // Insert new user WITHOUT PASSWORD
        $query = "INSERT INTO users (name, email, role) VALUES ('$name', '$email', '$role')";
        if (mysqli_query($conn, $query)) {
            header("Location: otp_login.php?success=Account created! Login using OTP.");
            exit();
        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

h2 {
    text-align: center;
    color: #333;
}

form {
    background: #fff;
    padding: 50px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    display: flex;
    flex-direction: column;
}

input, select, button {
    margin: 10px 0;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

input:focus, select:focus {
    outline: none;
    border-color: #007bff;
}

button {
    background-color: #007bff;
    color: white;
    cursor: pointer;
    border: none;
    transition: background 0.3s;
}

button:hover {
    background-color: #0056b3;
}

p {
    text-align: center;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.error-message {
    color: red;
    text-align: center;
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
    
    <form id="registerForm" method="POST">
    <h2>Register</h2>
        <input type="text" name="name" placeholder="Enter Full Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        
        <select name="role" required>
            <option value="farmer">Farmer</option>
            <option value="buyer">Buyer</option>
            <option value="admin">Admin</option>
        </select>
        
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login</a></p>

    </form>

   
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <script>
        $(document).ready(function () {
            $("#registerForm").submit(function (e) {
                var password = $("#password").val();
                var confirmPassword = $("#confirm_password").val();

                if (password !== confirmPassword) {
                    alert("Passwords do not match!");
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>