<?php
session_start(); // Start the session
include "firebase.php"; // Firebase Connection

$errorMessage = "";
$itemId = $name = $price = $description = $imageBase64 = $Searchname = $dbcategory = $dbseason = "";
$showFormElements = false;

// Handle search functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["Searchname"])) {
    $Searchname = trim($_POST["Searchname"]);

    if (empty($Searchname)) {
        $errorMessage = "Please enter the Name of Item before proceeding.";
    } else {
        // 🔥 Search for the item in Firestore
        $menuRef = $database->getReference('menu')->getValue();

        if ($menuRef) {
            foreach ($menuRef as $key => $item) {
                if (isset($item['name']) && $item['name'] === $Searchname) {
                    // Fetch data from Firestore
                    $name = $item['name'];
                    $price = $item['price'];
                    $description = $item['description'];
                    $imageBase64 = $item['imageBase64'] ?? ""; // Base64 image
                    $itemId = $key; // Firestore key
                    $dbcategory = $item['category'];
                    $dbseason = $item['season'];

                    $_SESSION['itemId'] = $itemId; // Store the Firestore item key
                    $showFormElements = true;
                    break;
                }
            }
        } 
        
        if (!$showFormElements) {
            $errorMessage = "Item not found in the database.";
        }
    }
}

// Handle delete functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteItem"])) {
    // Get the Firestore item key from session
    if (isset($_SESSION['itemId'])) {
        $itemId = $_SESSION['itemId'];
    } else {
        $errorMessage = "Error: No item ID found in the session.";
        exit();
    }

    // 🔥 Delete the item from Firestore
    $database->getReference("menu/{$itemId}")->remove();

    $_SESSION['message'] = "Item deleted successfully!";
    $showFormElements = false; // Hide form after deletion
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Menu Item</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admineditmenu.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Delete Menu Item</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="Searchname" class="form-label">Enter Menu Item Name:</label>
                <input type="text" name="Searchname" id="Searchname" class="form-control" value="<?php echo htmlspecialchars($Searchname); ?>" placeholder="Search for an item" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Search</button>
            <p id="result"><?php echo htmlspecialchars($errorMessage); ?></p>
        </form>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
            <div class="<?php echo $showFormElements ? '' : 'hidden-elements'; ?>">
                <div class="mb-3">
                    <label for="category" class="form-label">Category:</label>
                    <select name="category" id="category" class="form-select" disabled>
                        <option value="nil" <?php echo $dbcategory == 'nil' ? 'selected' : ''; ?>>Nil</option>
                        <option value="set" <?php echo $dbcategory == 'set' ? 'selected' : ''; ?>>Set</option>
                        <option value="alacarte" <?php echo $dbcategory == 'alacarte' ? 'selected' : ''; ?>>Ala-carte</option>
                        <option value="sides" <?php echo $dbcategory == 'sides' ? 'selected' : ''; ?>>Sides</option>
                        <option value="beverages" <?php echo $dbcategory == 'beverages' ? 'selected' : ''; ?>>Beverages</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="season" class="form-label">Season:</label>
                    <select name="season" id="season" class="form-select" disabled>
                        <option value="nil" <?php echo $dbseason == 'nil' ? 'selected' : ''; ?>>Nil</option>
                        <option value="CNY" <?php echo $dbseason == 'CNY' ? 'selected' : ''; ?>>Chinese New Year</option>
                        <option value="hari_raya" <?php echo $dbseason == 'hari_raya' ? 'selected' : ''; ?>>Hari Raya</option>
                        <option value="deepavali" <?php echo $dbseason == 'deepavali' ? 'selected' : ''; ?>>Deepavali</option>
                        <option value="halloween" <?php echo $dbseason == 'halloween' ? 'selected' : ''; ?>>Halloween</option>
                        <option value="xmas" <?php echo $dbseason == 'xmas' ? 'selected' : ''; ?>>Christmas</option>
                        <option value="vday" <?php echo ($dbseason === 'vday' ? 'selected' : ''); ?>>Valentine</option>
                        <option value="lantern" <?php echo ($dbseason === 'lantern' ? 'selected' : ''); ?>>Mid-Autumn Festival</option>
                    </select>
                </div>

                <?php if (!empty($imageBase64)): ?>
                    <div class="mb-3 text-center">
                        <label class="form-label">Current Image:</label>
                        <div>
                            <img src="data:image/jpeg;base64,<?php echo $imageBase64; ?>" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="itemname" class="form-label">Name of Item:</label>
                    <input type="text" name="itemname" id="itemname" class="form-control" value="<?php echo htmlspecialchars($name); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price:</label>
                    <input type="text" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" class="form-control" rows="3" readonly><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <button type="submit" name="deleteItem" class="btn btn-primary w-100" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
            </div>
        </form>
    </div>

    <div class="text-center mt-4">
        <a href="AdminMain.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
