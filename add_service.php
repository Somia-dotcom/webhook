<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $service_type = $_POST['service_type'];
    $rental_category = isset($_POST['rental_category']) ? $_POST['rental_category'] : NULL;
    $machinery_selected = isset($_POST['machinery']) ? implode(", ", $_POST['machinery']) : NULL;
    $price = $_POST['price'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];

    // Ensure the upload directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle Image Upload
    $image_path = NULL;
    if (!empty($_FILES["image"]["name"])) {
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = $target_dir . $image_name;

        // Move file to uploads directory
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            die("Error uploading image.");
        }
    }

    // Store only the filename in the database, not the full path
    $image_filename = isset($image_name) ? $image_name : NULL;

    // Prepare SQL Query
    $sql = "INSERT INTO services (name, service_type, rental_category, machinery_selected, price, description, image, contact) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    // Bind Parameters
    $stmt->bind_param("ssssdsss", $name, $service_type, $rental_category, $machinery_selected, $price, $description, $image_filename, $contact);

    // Execute Query
    if ($stmt->execute()) {
        echo "<script>alert('Service added successfully!'); </script>";
    } else {
        echo "Error adding service: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/agro_icon.png" type="image/x-icon">
    <title>AgroBazaar</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/7860568151.js" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<style>
 header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: green;
    color: white;
    padding: 10px 20px;
}
/* Form Container */
form {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

/* Input Fields */
form input,
form select,
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

/* File Input */
input[type="file"] {
    border: none;
    background: #fff;
}

/* Select Dropdown */
form select {
    cursor: pointer;
}

/* Submit Button */
form button {
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

form button:hover {
    background: #218838;
}

/* Form Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Responsive Design */
@media (max-width: 600px) {
    form {
        width: 90%;
    }
}
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
<header>
        <h1>AgroBazaar</h1>
        <a href="profile.php" style="display: inline-block; background-color:rgb(244, 20, 20); color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; transition: all 0.3s ease-in-out;" 
   onmouseover="this.style.backgroundColor='rgb(244, 20, 20)'; this.style.boxShadow='0px 0px 10pxrgb(244, 20, 20)'; this.style.transform='scale(1.05)';" 
   onmouseout="this.style.backgroundColor=' rgb(244, 20, 20)'; this.style.boxShadow='none'; this.style.transform='scale(1)';">
   Back to Home
</a>
       
        
    </header>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="product.php">Products</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="schemes.php">Schemes</a></li>
        <li><a href="report.php">Report</a></li>
        <a href="cart.php" class="btn btn-warning"><i class="fas fa-shopping-cart"></i> Cart</a>
    </ul>
    
</nav>

    <main id="main-site">

        <!-- Service Submission Form -->
        <section class="container py-5">
            <h4 class="text-center">Add a New Service</h4>
            <form action="add_service.php" method="post" enctype="multipart/form-data" class="p-3 border rounded">
                <input type="text" name="name" placeholder="Service Name" class="form-control mb-2" required>
                <select name="service_type" class="form-select mb-2" id="serviceTypeSelect" onchange="toggleServiceOptions()" required>
                <option value="Machine Rental">Select option</option>
                    <option value="Machine Rental">Machine Rental</option>
                    <option value="Training">Training</option>
                    <option value="Repair Services">Repair Services</option>
                </select>
                
                <div id="machineRentalForm" style="display: none;">
                    <h5>Select Rental Category</h5>
                    <select name="rental_category" class="form-select mb-2" onchange="toggleRentalOptions()">
                    <option value="Land Development">Select option</option>
                        <option value="Land Development">Land Development Machinery Hire Charges</option>
                        <option value="Minor Irrigation">Minor Irrigation Machinery Hire Charges</option>
                    </select>
                    
                    <div id="landDevelopmentOptions" style="display: none;">
                        <h6>Select Machinery (Multiple Selection Allowed)</h6>
                        <input type="checkbox" name="machinery[]" value="Tractor"> Tractor (₹265/hour)<br>
                        <input type="checkbox" name="machinery[]" value="Bulldozer"> Bulldozer (₹670/hour)<br>
                        <input type="checkbox" name="machinery[]" value="Tractor Mounted Combine Harvester"> Tractor Mounted Combine Harvester (₹780/hour)<br>
                        <input type="checkbox" name="machinery[]" value="Class Crop Tiger Combine Harvester"> Class Crop Tiger Combine Harvester (₹1130/hour)<br>
                        <input type="checkbox" name="machinery[]" value="Hydraulic Excavator (BE 71)"> Hydraulic Excavator (BE 71) (₹580/hour)<br>
                        <input type="checkbox" name="machinery[]" value="Hydraulic Excavator L & T (KOMATSU)"> Hydraulic Excavator L & T (KOMATSU) (₹970/hour)<br>
                    </div>
                    <div id="minorIrrigationOptions" style="display: none;">
                        <h6>Select Machinery (Multiple Selection Allowed)</h6>
                        <input type="checkbox" name="machinery[]" value="Percussion Drill"> Percussion Drill (₹300/day)<br>
                        <input type="checkbox" name="machinery[]" value="Rotary Drill"> Rotary Drill (₹120/m)<br>
                        <input type="checkbox" name="machinery[]" value="Rock Blasting Unit"> Rock Blasting Unit (₹250/blasting)<br>
                        <input type="checkbox" name="machinery[]" value="Long Hole Equipment"> Long Hole Equipment (₹250/day)<br>
                        <input type="checkbox" name="machinery[]" value="Resistivity Meter"> Resistivity Meter (₹500/point)<br>
                        <input type="checkbox" name="machinery[]" value="Electrical Logger"> Electrical Logger (₹1000/tube well)<br>
                    </div>
                </div>
                
                <input type="number" name="price" placeholder="Service Price (₹)" class="form-control mb-2" required>
                <textarea name="description" placeholder="Description" class="form-control mb-2"></textarea>
                <input type="file" name="image" accept="image/*" class="form-control mb-2" required>
                <input type="contact" name="contact" placeholder="contact" class="form-control mb-2" required>
                <button type="submit" class="btn btn-success w-100">Add Service</button>
            </form>
        </section>
    </main>

   

    <script>
        function toggleServiceOptions() {
            let serviceType = document.getElementById("serviceTypeSelect").value;
            let rentalOptions = document.getElementById("machineRentalForm");
            rentalOptions.style.display = (serviceType === "Machine Rental") ? "block" : "none";
        }

        function toggleRentalOptions() {
            let rentalCategory = document.querySelector("select[name='rental_category']").value;
            document.getElementById("landDevelopmentOptions").style.display = (rentalCategory === "Land Development") ? "block" : "none";
            document.getElementById("minorIrrigationOptions").style.display = (rentalCategory === "Minor Irrigation") ? "block" : "none";
        }
    </script>
</body>
</html>
