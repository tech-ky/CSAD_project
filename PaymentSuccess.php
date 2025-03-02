<?php
session_start();
include "firebase.php"; // Ensure Firebase Firestore connection

// Ensure session data exists
if (!isset($_SESSION['email']) || !isset($_SESSION['PaymentMethod'])) {
    echo "Invalid access. No session data found.";
    exit;
}

$email = $_SESSION['email'];
$paymentMethod = $_SESSION['PaymentMethod'];


// ✅ Clear the cart session after successful payment
unset($_SESSION['cart']);
unset($_SESSION['PaymentMethod']);

// Fetch the last stored order using the email
try {
    $orderRef = $database->getReference('orders')
        ->orderByChild('email')
        ->equalTo($email)
        ->limitToLast(1)
        ->getValue();
} catch (Exception $e) {
    echo "Error retrieving order: " . $e->getMessage();
    exit;
}

// Ensure order data is retrieved
if (!$orderRef || empty($orderRef)) {
    echo "No recent orders found for this email.";
    exit;
}

// Get the latest order
$orderData = reset($orderRef);

// Extract order details safely
$items = $orderData['items'] ?? [];
$subtotal = $orderData['subtotal'] ?? 0.00;
$gst = $orderData['gst'] ?? 0.00;
$serviceCharge = $orderData['serviceCharge'] ?? 0.00;
$totalAmount = $orderData['totalAmount'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background-color: #f8f9fa;
        font-family: "Courier New", Courier, monospace;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-image: url('HomePictures/background.jpg');
        background-size: cover;
        background-position: center;
    }

    .receipt-container {
        width: 550px;
        background: white;
        background-image: url('HomePictures/paper.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        border: 2px solid #ddd;
        text-align: center;
    }

    .receipt-header {
        font-size: 22px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    .payment-success {
        font-size: 18px;
        font-weight: bold;
        color: green;
        margin-bottom: 10px;
    }

    .email-receipt {
        font-size: 16px;
        margin-bottom: 15px;
    }

    .dashed-line {
        border-top: 2px dashed black;
        margin: 15px 0;
    }

    table {
        width: 100%;
        font-size: 16px;
        text-align: left;
    }

    th, td {
        padding: 6px 0;
    }

    .total {
        font-weight: bold;
        font-size: 18px;
    }

    .barcode {
        margin-top: 20px;
        width: 100%;
        height: 100px;
        background-image: url('HomePictures/barcode.jpg');
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    .btn-return {
        background: black;
        color: white;
        text-transform: uppercase;
        width: 100%;
        padding: 12px;
        font-size: 16px;
        margin-top: 15px;
        border: none;
    }

    .btn-return:hover {
        background-color: darkgray;
    }

    .item-remarks {
        font-size: 14px;
        color: gray;
        margin-top: -5px;
    }
</style>
<body>
    <div class="receipt-container">
        <div class="receipt-header">Payment Success</div>
        <div class="payment-success">Payment by <strong><?= htmlspecialchars($paymentMethod); ?></strong> is successful!</div>
        <div class="email-receipt">Receipt sent to <strong><?= htmlspecialchars($email); ?></strong></div>
        <div class="dashed-line"></div>

        <table>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($item['name'] ?? 'Unknown Item'); ?></strong>
                            <?php if (!empty($item['remarks'])): ?>
                                <div class="item-remarks"> <?= htmlspecialchars($item['remarks']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['quantity'] ?? '1'); ?></td>
                        <td>
                            <?php if (!isset($item['price'])): ?>
                                <span style='color:red;'>Error: Missing Price</span>
                            <?php else: ?>
                                $<?= number_format($item['price'] * $item['quantity'], 2); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <table>
            <tr><td>Subtotal</td><td>$<?= number_format($subtotal, 2); ?></td></tr>
            <tr><td>GST (9%)</td><td>$<?= number_format($gst, 2); ?></td></tr>
            <tr><td>Service Charge (10%)</td><td>$<?= number_format($serviceCharge, 2); ?></td></tr>
            <tr class="total"><td>Total</td><td>$<?= number_format($totalAmount, 2); ?></td></tr>
        </table>

        <div class="dashed-line"></div>
        <div class="barcode"></div>
        <a href="Home.php" class="btn-return">Return to Home</a>
    </div>
</body>
</html>
