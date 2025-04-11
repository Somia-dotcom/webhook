<?php
$conn = mysqli_connect("localhost", "root", "", "agro_bazaar", 3308);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
