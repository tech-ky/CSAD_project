<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="css/mainAdmin.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- Admin Dashboard Header with Button -->
    <div class="header">
        <h1>Admin Dashboard</h1>
        <button onclick="window.location.href='SignIn.php'">Back to Home</button>
    </div>

    <!-- Card Container -->
    <div class="card-container">
        <!-- Card 1: Add Items -->
        <div class="card">
            <h3>Add New Items</h3>
            <p>Upload new menu items with images, descriptions, and prices.</p>
            <button onclick="window.location.href='AdminAddMenu.php'">Go</button>
        </div>

        <!-- Card 2: Edit Items -->
        <div class="card">
            <h3>Edit Existing Items</h3>
            <p>Update or modify details of existing menu items.</p>
            <button onclick="window.location.href='AdminEditMenu.php'">Go</button>
        </div>

        <!-- Card 3: Delete Items -->
        <div class="card">
            <h3>Delete Existing Items</h3>
            <p>Delete existing menu items.</p>
            <button onclick="window.location.href='AdminDeleteMenu.php'">Go</button>
        </div>

        <!-- Card 4: View Orders -->
        <div class="card">
            <h3>View Order</h3>
            <p>View Users' Order</p>
            <button onclick="window.location.href='AdminViewOrder.php'">Go</button>
        </div>
        
        <!-- Card 5: Change Seasonal Promotions -->
        <div class="card">
            <h3>Change Seasonal Promotions</h3>
            <p>Update and manage seasonal promotions for specific holidays.</p>
            <button onclick="window.location.href='AdminSeasonalPromotions.php'">Go</button>
        </div>
    </div>
</body>
</html>