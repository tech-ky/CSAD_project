<?php
session_start();
include "firebase.php"; // Firebase Realtime Database Connection

// Initialize the cart if not already done
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the Content-Type is JSON
if (!isset($_SERVER['CONTENT_TYPE']) || stripos($_SERVER['CONTENT_TYPE'], 'application/json') === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Content-Type. Expected application/json']);
    exit;
}

// Read the raw input
$jsonData = file_get_contents('php://input');
if (!$jsonData) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received.']);
    exit;
}

// Decode the JSON data
$data = json_decode($jsonData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

// 🔥 Fetch all menu items from Realtime Database
$menuItems = $database->getReference('menu')->getValue();

foreach ($data as $item) {
    $itemId = $item['id'];
    $quantity = $item['quantity'];
    $side = $item['side'] ?? ''; // Get the side or default to an empty string
    $drink = $item['drink'] ?? ''; // Get the drink or default to an empty string

    // 🔥 Format remarks
    $formattedRemarks = ($side === "" && $drink === "") ? "" : "Side: $side, Drink: $drink";

    // 🔥 Fetch item details from Realtime Database
    $itemDetails = null;
    if ($menuItems) {
        foreach ($menuItems as $key => $menuItem) {
            // Assuming the key is the item ID and we need to match it
            if ($key == $itemId) {
                $itemDetails = $menuItem;
                break;
            }
        }
    }

    if ($itemDetails) {
        // Add item to session cart
        $_SESSION['cart'][] = [
            'id' => $itemId,
            'name' => $itemDetails['name'],
            'price' => $itemDetails['price'],
            'quantity' => $quantity,
            'description' => $itemDetails['description'],
            'remarks' => $formattedRemarks
        ];
    } else {
        // Handle case where item ID is invalid
        $_SESSION['cart'][] = [
            'id' => $itemId,
            'name' => 'Unknown Item',
            'price' => 0,
            'quantity' => $quantity,
            'description' => 'No description available',
            'remarks' => ''
        ];
    }
}

// Return the updated cart as a response
echo json_encode([
    'status' => 'success',
    'message' => 'Cart updated successfully',
    'cart' => $_SESSION['cart']
]);
?>