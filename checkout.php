<?php
session_start();
include "firebase.php"; // Firebase Firestore connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['PaymentMethod'])) {
    $email = $_POST['email'];
    $paymentMethod = $_POST['PaymentMethod'];

    // Calculate totals
    $subtotal = 0;
    $orderItems = [];

    foreach ($_SESSION['cart'] as $item) {
        $itemSubtotal = $item['price'] * $item['quantity'];
        $subtotal += $itemSubtotal;

        $orderItems[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'remarks' => $item['remarks'] ?? '', // Handle optional remarks
        ];
    }

    $gst = $subtotal * 0.09;
    $serviceCharge = $subtotal * 0.10;
    $total = $subtotal + $gst + $serviceCharge;

    // Generate unique order ID
    $orderId = 'order_' . date('YmdHis') . '_' . rand(1000, 9999);

    // Prepare order data
    $orderData = [
        'orderId' => $orderId,
        'email' => $email,
        'paymentMethod' => $paymentMethod,
        'items' => $orderItems,
        'subtotal' => $subtotal,
        'gst' => $gst,
        'serviceCharge' => $serviceCharge,
        'totalAmount' => $total,
        'timestamp' => date('Y-m-d H:i:s'),
    ];

    // Store order in Firebase
    try {
        $database->getReference('orders/' . $orderId)->set($orderData);
    } catch (Exception $e) {
        die("Error storing order in database: " . $e->getMessage());
    }

    // Store session variables for payment redirection
    $_SESSION['email'] = $email;
    $_SESSION['PaymentMethod'] = $paymentMethod;

    // Redirect to PaymentRedirection.php
    header("Location: PaymentRedirection.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        position: relative;
    }

    .receipt-header {
        font-size: 22px;
        font-weight: bold;
        text-transform: uppercase;
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

    .form-group {
        text-align: left;
        margin-top: 15px;
    }

    input[type="text"], select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .btn-checkout {
        background: black;
        color: white;
        text-transform: uppercase;
        width: 100%;
        padding: 12px;
        font-size: 16px;
        margin-top: 15px;
        border: none;
    }

    .btn-checkout:hover {
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
        <div class="receipt-header">Checkout</div>
        <div class="dashed-line"></div>

        <table>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $item): 
                    $itemSubtotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemSubtotal;
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($item['name']); ?></strong>
                        <?php if (!empty($item['remarks'])): ?>
                            <div class="item-remarks"><?= htmlspecialchars($item['remarks']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?= number_format($item['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <?php 
        $gst = $subtotal * 0.09;
        $serviceCharge = $subtotal * 0.10;
        $total = $subtotal + $gst + $serviceCharge;
        ?>

        <table>
            <tr>
                <td>Subtotal</td>
                <td>$<?= number_format($subtotal, 2); ?></td>
            </tr>
            <tr>
                <td>GST (9%)</td>
                <td>$<?= number_format($gst, 2); ?></td>
            </tr>
            <tr>
                <td>Service Charge (10%)</td>
                <td>$<?= number_format($serviceCharge, 2); ?></td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td>$<?= number_format($total, 2); ?></td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <!-- Checkout Form -->
        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="email"><strong>Email:</strong></label>
                <input type="text" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="PaymentMethod"><strong>Payment Method:</strong></label>
                <select name="PaymentMethod" id="PaymentMethod">
                    <option value="Visa">Visa</option>
                    <option value="Master">Master</option>
                    <option value="Amex">Amex</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>

            <button type="submit" class="btn-checkout">Place Order</button>
        </form>
    </div>
</body>
</html>
