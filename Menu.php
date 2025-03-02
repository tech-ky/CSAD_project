<?php
session_start();
include "firebase.php"; // Realtime Database connection

// Fetch the current season set by the admin
$currentSeason = $database->getReference('settings/season')->getValue() ?? "nil";

// Get category from the URL (default to 'set')
$category = isset($_GET['category']) ? $_GET['category'] : 'set';
$message = '';
$files = [];
$seasonItems = [];

// 🔥 Fetch sides and beverages from Realtime Database
$sidesOptions = [];
$beveragesOptions = [];

$menuItems = $database->getReference('menu')->getValue();
if ($menuItems) {
    foreach ($menuItems as $item) {
        if (isset($item['category']) && isset($item['stock']) && $item['stock'] === "For Sale") {
            if ($item['category'] === 'sides') {
                $sidesOptions[] = $item['name'];
            } elseif ($item['category'] === 'beverages') {
                $beveragesOptions[] = $item['name'];
            }
        }
    }
}

// 🔥 Fetch items based on category or season
if (isset($_GET['menu']) && $_GET['menu'] == 'season') {
    if ($currentSeason !== "nil") {
        foreach ($menuItems as $item) {
            if (isset($item['season']) && $item['season'] === $currentSeason) {
                $seasonItems[] = $item;
            }
        }
    } else {
        $message = "No seasonal promotions available.";
    }
} else {
   foreach ($menuItems as $key => $item) {
    if (is_array($item) && isset($item['category']) && $item['category'] === $category && isset($item['stock']) && $item['stock'] === "For Sale") {
        // Ensure the item has an ID
        $item['id'] = $key; // Assign the Firebase key as ID
        $files[] = $item;
    }
}


    if (empty($files)) {
        $message = "No items found in this category.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/topnavigation.css" rel="stylesheet" type="text/css">
    <link href="css/menu.css" rel="stylesheet" type="text/css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap');
        body{
            background-image:url("HomePictures/background.jpg");
        }
        
        #Menu-heading{
             font-family: "Permanent Marker", serif;
        }
        
        .category-links{
             font-family: "Permanent Marker", serif;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar">
        <div class="top-nav">
            <ul>
                <li><img src="HomePictures/logo.png" alt="Logo"></li>
                <li><button onclick="window.location.href='SignIn.php'">Sign In</button></li>
            </ul>
            <ul>
                <li><a href="Home.php" class='btn btn-outline-primary'>Home</a></li>
                <li><a href="Menu.php" class='btn btn-primary' style='color: white'>Menu</a></li>
                <li><a href="Location.php" class='btn btn-outline-primary'>Locations</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 id="Menu-heading" class="text-center" style="color: white">Menu Items</h2>

        <!-- Category Links -->
        <div class="category-links">
            <a style="color: white; " href="menu.php?menu=season" class="<?= (isset($_GET['menu']) && $_GET['menu'] == 'season') ? 'btn btn-primary' : 'btn btn-outline-primary'; ?>">Season</a>
            <a style="color: white;" href="menu.php?category=set" class="<?= ($category === 'set' && !isset($_GET['menu'])) ? 'btn btn-primary' : 'btn btn-outline-primary'; ?>">Set</a>
            <a style="color: white;" href="menu.php?category=alacarte" class="<?= ($category === 'alacarte' && !isset($_GET['menu'])) ? 'btn btn-primary' : 'btn btn-outline-primary'; ?>">Ala-carte</a>
            <a style="color: white;" href="menu.php?category=sides" class="<?= ($category === 'sides' && !isset($_GET['menu'])) ? 'btn btn-primary' : 'btn btn-outline-primary'; ?>">Sides</a>
            <a style="color: white;" href="menu.php?category=beverages" class="<?= ($category === 'beverages' && !isset($_GET['menu'])) ? 'btn btn-primary' : 'btn btn-outline-primary'; ?>">Beverages</a>
            <a href="cart.php"><i class="fa fa-shopping-cart" style="font-size:36px"></i></a>
        </div>

        <!-- Display Items -->
        <?php if (!isset($_GET['menu']) || $_GET['menu'] != 'season'): ?>
            <?php if (!empty($message)): ?>
                <div class="alert alert-warning text-center"><?= $message; ?></div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php foreach ($files as $item): ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="data:image/jpeg;base64,<?= $item['imageBase64']; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="card-img-top">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($item['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($item['description']); ?></p>
                                    <p class="price">$<?= htmlspecialchars($item['price']); ?></p>
                                </div>
                                <div class="card-footer text-end">
                                  <button class="btn btn-primary" 
    onclick="showModal('<?= htmlspecialchars($item['name'] ?? 'Unknown Item'); ?>', 
                        '<?= htmlspecialchars($item['price'] ?? '0'); ?>', 
                        '<?= $item['imageBase64'] ?? ''; ?>', 
                        '<?= $category; ?>', 
                        '<?= isset($item['id']) ? htmlspecialchars($item['id']) : 'unknown'; ?>')">
    Add to Cart
</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Display Seasonal Items -->
        <?php if (isset($_GET['menu']) && $_GET['menu'] == 'season'): ?>
            <?php if (!empty($message)): ?>
                <div class="alert alert-warning text-center"><?= $message; ?></div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php foreach ($seasonItems as $item): ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="data:image/jpeg;base64,<?= $item['imageBase64']; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="card-img-top">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($item['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($item['description']); ?></p>
                                    <p class="price">$<?= htmlspecialchars($item['price']); ?></p>
                                </div>
                                <div class="card-footer text-end">
                                 <button class="btn btn-primary" 
    onclick="showModal('<?= htmlspecialchars($item['name'] ?? 'Unknown Item'); ?>', 
                        '<?= htmlspecialchars($item['price'] ?? '0'); ?>', 
                        '<?= $item['imageBase64'] ?? ''; ?>', 
                        '<?= $category; ?>', 
                        '<?= isset($item['id']) ? htmlspecialchars($item['id']) : 'unknown'; ?>')">
    Add to Cart
</button>
                        </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToCartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalItemImage" src="" alt="Item Image" class="img-fluid mb-3" style="max-height: 200px; border-radius: 10px;">
                <p><strong>Item:</strong> <span id="modalItemName"></span></p>
                <p><strong>Price:</strong> <span id="modalItemPrice"></span></p>

                <!-- Display Sides and Drinks only for Sets -->
                <div id="setOptions" style="display: none;">
                    <div class="mb-3">
                        <label for="sideOption" class="form-label">Choose a Side:</label>
                        <select id="sideOption" class="form-select">
                            <?php foreach ($sidesOptions as $side): ?>
                                <option value="<?php echo htmlspecialchars($side); ?>"><?php echo htmlspecialchars($side); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="drinkOption" class="form-label">Choose a Drink:</label>
                        <select id="drinkOption" class="form-select">
                            <?php foreach ($beveragesOptions as $drink): ?>
                                <option value="<?php echo htmlspecialchars($drink); ?>"><?php echo htmlspecialchars($drink); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="d-flex justify-content-center align-items-center">
                    <button class="btn btn-secondary" onclick="decreaseQuantity()">-</button>
                    <span id="modalItemQuantity" class="mx-3">1</span>
                    <button class="btn btn-secondary" onclick="increaseQuantity()">+</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="AddToCart()">Add</button>
            </div>
        </div>
    </div>
</div>

<script>
    let quantity = 1;

    // Function to show the modal with dynamic content
  function showModal(itemName, itemPrice, itemImageBase64, category, itemId) {
    document.getElementById('modalItemImage').src = "data:image/jpeg;base64," + itemImageBase64;
    document.getElementById('modalItemName').innerText = itemName;
    document.getElementById('modalItemName').setAttribute('data-id', itemId); 
    document.getElementById('modalItemPrice').innerText = '$' + itemPrice;
    document.getElementById('modalItemName').setAttribute('data-category', category);
    
    quantity = 1;
    document.getElementById('modalItemQuantity').innerText = quantity;

    if (category === 'set' && !window.location.href.includes('menu=season')) {
        document.getElementById('setOptions').style.display = 'block';
    } else {
        document.getElementById('setOptions').style.display = 'none';
    }

    const myModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
    myModal.show();
    console.log("Modal Opened - Name:", itemName, "Price:", itemPrice, "ID:", itemId, "Category:", category);

}


    // Function to increase the quantity
    function increaseQuantity() {
        quantity++;
        document.getElementById('modalItemQuantity').innerText = quantity;
    }

    // Function to decrease the quantity
    function decreaseQuantity() {
        if (quantity > 1) {
            quantity--;
            document.getElementById('modalItemQuantity').innerText = quantity;
        }
    }

    var selectedItemIds = [];
function AddToCart() {
    var itemid = document.getElementById('modalItemName').getAttribute('data-id');
    var itemname = document.getElementById('modalItemName').innerText;
    var itemprice = document.getElementById('modalItemPrice').innerText.replace('$', '');
    var quantity = parseInt(document.getElementById('modalItemQuantity').innerText);
    var category = document.getElementById('modalItemName').getAttribute('data-category');

    var selectedSide = '';
    var selectedDrink = '';

    if (category === 'set'&& !window.location.href.includes('menu=season')) {
        selectedSide = document.getElementById('sideOption').value;
        selectedDrink = document.getElementById('drinkOption').value;
    }

    if (!itemid || !itemname || !itemprice) {
        alert("Error: Missing item details!");
        return;
    }

    // Add item to the cart with current quantity
    selectedItemIds.push({ 
        id: itemid, 
        name: itemname, 
        price: itemprice, 
        quantity: quantity, 
        side: selectedSide, 
        drink: selectedDrink 
    });

    console.log('Sending data to server:', JSON.stringify(selectedItemIds));

    // Reset the quantity back to 1 after adding to the cart
    quantity = 1;
    document.getElementById('modalItemQuantity').innerText = quantity;

    // Send data to the server
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(selectedItemIds),
    })
    .then(response => response.text())
    .then(data => {
        alert('Item added to cart successfully!');
        console.log('Server response:', data);
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
    });
    
    selectedItemIds = [];
}

        
    function addtoidarray(itemid, quantity, selectedSide, selectedDrink) {
        selectedItemIds.push({ id: itemid, quantity: quantity, side: selectedSide, drink: selectedDrink });
        alert(`Item added to cart. Item ID: ${itemid}, Quantity: ${quantity}, Side: ${selectedSide}, Drink: ${selectedDrink} `);

        console.log('Sending data to server:', JSON.stringify(selectedItemIds));

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Set the Content-Type header
            },
            body: JSON.stringify(selectedItemIds), // Send the array as a JSON string
        })
        .then(response => response.text()) // Parse response as plain text for debugging
        .then(data => {
            alert('Item added to cart successfully!');
            console.log('Server response:', data);
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
        });
        console.log('received:', JSON.stringify(selectedItemIds));
    }
</script>

    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>