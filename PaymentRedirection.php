<?php
session_start();
include "firebase.php"; // Firebase Firestore connection

// Ensure required session variables exist
if (!isset($_SESSION['cart'])) {
    echo "Missing Cart";
    exit;
}

if (!isset($_SESSION['PaymentMethod']) || !isset($_SESSION['email'])) {
    echo "Missing Payment Method or Email";
    exit;
}

$email = $_SESSION['email'];
$paymentMethod = $_SESSION['PaymentMethod'];
$cart = $_SESSION['cart'];

// Calculate totals dynamically
$subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
$gst = $subtotal * 0.09;
$serviceCharge = $subtotal * 0.10;
$totalAmount = $subtotal + $gst + $serviceCharge;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment in Progress</title>
    <link href="css/PaymentRedirection.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="container mt-5">
    <?php
    if ($paymentMethod == 'Cash') {
        echo "<h1>Proceed to Counter</h1>";
        unset($_SESSION['cart']); // Clear cart after processing
        echo "<script type='text/javascript'>
                setTimeout(function() {
                    alert('Back to Home');
                    window.location.href = 'Home.php';
                }, 5000);
              </script>";
    } else {
        echo "<h1>Redirecting...</h1>";
        echo "<h3>Processing Payment via <strong>" . htmlspecialchars($paymentMethod) . "</strong>...</h3>";
        echo "<h3>Receipt will be sent to <strong>" . htmlspecialchars($email) . "</strong></h3>";
        echo "<meta http-equiv='refresh' content='5; url=PaymentSuccess.php' />";
    }
    ?>
</div>

<div class="loader"></div> <!-- Loader always present -->

</body>
</html>
