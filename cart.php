<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $itemId = $_POST['delete'];
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['id'] === $itemId) {
                unset($_SESSION['cart'][$index]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } elseif (isset($_POST['update'])) {
        $itemId = $_POST['update'];
        $newQuantity = (int)$_POST['quantity'];
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['id'] === $itemId) {
                $_SESSION['cart'][$index]['quantity'] = $newQuantity;
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
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

    .back-button {
        background: black;
        color: white;
        border: none;
        padding: 6px 12px;
        font-size: 14px;
        position: absolute;
        top: 10px;
        left: 10px;
        border-radius: 5px;
    }

    .back-button:hover {
        background-color: darkgray;
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

    .barcode {
        margin-top: 20px;
        width: 100%;
        height: 100px;
        background-image: url('HomePictures/barcode.jpg');
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .btn-action {
        background: black;
        color: white;
        border: none;
        padding: 5px 10px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 3px;
        width: 100px;
    }

    .btn-action:hover {
        background-color: darkgray;
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

    input[type="number"] {
        width: 60px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 3px;
    }

    .item-remarks {
        font-size: 14px;
        color: gray;
        margin-top: -5px;
    }
</style>
<body>
    <div class="receipt-container">
        <button class="back-button" onclick="window.location.href='menu.php'">Back</button>
        <div class="receipt-header">Receipt</div>
        <div class="dashed-line"></div>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table>
                <tbody>
                    <?php 
                        $Distinct_items_array = [];

                        foreach ($_SESSION['cart'] as $item) { 
                            $found = false;
                            foreach ($Distinct_items_array as &$distinct_item) { 
                                if ($item['name'] === $distinct_item['name']) { 
                                    $distinct_item['quantity'] += $item['quantity']; 
                                    $found = true;
                                    break;
                                }
                            }

                            if (!$found) {
                                $Distinct_items_array[] = $item; 
                            } 
                        }
                        $_SESSION['cart'] = $Distinct_items_array;

                    $subtotal = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $itemSubtotal = $item['price'] * $item['quantity'];
                        $subtotal += $itemSubtotal;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            <?php if (!empty($item['remarks'])): ?>
                                <div class="item-remarks"><?php echo htmlspecialchars($item['remarks']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo '$'.number_format($itemSubtotal, 2); ?></td>
                        <td>
                            <form id="updateForm-<?php echo htmlspecialchars($item['id']); ?>" method="post" style="display: inline-block;">
                                <input type="hidden" name="update" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                            </form>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn-action" onclick="document.getElementById('updateForm-<?php echo htmlspecialchars($item['id']); ?>').submit();">Save Edit</button>
                                <form method="post" style="display: inline-block;">
                                    <button type="submit" name="delete" value="<?php echo htmlspecialchars($item['id']); ?>" class="btn-action">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="dashed-line"></div>
            
            <?php 
            $gst = $subtotal * 0.09;
            $servicecharge = $subtotal * 0.10;
            $total = $subtotal + $gst + $servicecharge;
            ?>

            <table>
                <tr>
                    <td>Subtotal</td>
                    <td><?php echo '$'.number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td>GST (9%)</td>
                    <td><?php echo '$'.number_format($gst, 2); ?></td>
                </tr>
                <tr>
                    <td>Service Charge (10%)</td>
                    <td><?php echo '$'.number_format($servicecharge, 2); ?></td>
                </tr>
                <tr class="total">
                    <td>Total</td>
                    <td><?php echo '$'.number_format($total, 2); ?></td>
                </tr>
            </table>

            <div class="dashed-line"></div>

            <div class="barcode"></div>

            <form method="post" action="checkout.php">
                <button class="btn-checkout" type="submit">Check Out</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
