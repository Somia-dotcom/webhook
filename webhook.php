<?php
$data = json_decode(file_get_contents('php://input'), true);
$intent = $data['queryResult']['intent']['displayName'];

$responseText = "Sorry, I didn't understand.";

if ($intent === "Buy Product") {
    $responseText = "You can buy products from the AgroBazaar product section.";
} elseif ($intent === "Book Service") {
    $responseText = "To book a service, please visit the service category and click 'Book'.";
} elseif ($intent === "View Schemes") {
    $responseText = "You can find available government schemes under the 'Schemes' section.";
}

$response = [
    "fulfillmentText" => $responseText
];

header('Content-Type: application/json');
echo json_encode($response);
?>
