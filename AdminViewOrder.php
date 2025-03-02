<?php
session_start();
include "firebase.php"; // Realtime Database connection

// Handle clear order request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_order_id'])) {
    $order_id_to_clear = $_POST['clear_order_id'];

    // Delete the order document in Realtime Database
    try {
        $database->getReference('orders/' . $order_id_to_clear)->remove();
    } catch (Exception $e) {
        die("Error deleting order: " . $e->getMessage());
    }
}

// Fetch all orders grouped by order_id
$orders = [];
try {
    $ordersSnapshot = $database->getReference('orders')->getValue(); // Fetch orders from Realtime Database
    if ($ordersSnapshot) {
        foreach ($ordersSnapshot as $order_id => $order_data) {
            $orders[$order_id] = $order_data;
        }
    }
} catch (Exception $e) {
    die("Error fetching orders from Realtime Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body style="  background-image: url('HomePictures/AdminBack.jpg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover; ">
    <div class="container mt-4">
        <h2 class="text-center" style="color: white">Admin: View All Orders</h2>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order_id => $order_data): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Order ID: <?php echo htmlspecialchars($order_id); ?></h5>
                            <p>Email: <?php echo htmlspecialchars($order_data['email']); ?></p>
                        </div>
                        <form method="post" onsubmit="return confirm('Are you sure you want to clear this order?');">
                            <input type="hidden" name="clear_order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                            <button type="submit" class="btn btn-danger">Clear Order</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_data['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($item['remarks'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">No orders found.</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="AdminMain.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
