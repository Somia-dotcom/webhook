<?php
// webhook.php
header("Content-Type: application/json");

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "agro_bazaar", 3308); // Change credentials accordingly
if ($conn->connect_error) {
    echo json_encode(["fulfillmentText" => "Database connection failed!"]);
    exit;
}

// Read Dialogflow JSON
$request = json_decode(file_get_contents("php://input"), true);
$intent = $request["queryResult"]["intent"]["displayName"];

$responseText = "Sorry, I didn't understand.";

// Handle intent: ShowProducts
if ($intent === "ShowProducts") {
    $sql = "SELECT name, quantity, unit, price, category FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $responseText = "Here are some available products:\n";
        while ($row = $result->fetch_assoc()) {
            $responseText .= "- " . $row["name"] . " (" . $row["quantity"] . $row["unit"] . ") - ₹" . $row["price"] . " [" . $row["category"] . "]\n";
        }
    } else {
        $responseText = "No products are available at the moment.";
    }
}

// Send response
echo json_encode(["fulfillmentText" => $responseText]);
?>
