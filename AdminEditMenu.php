<?php
session_start();
include "firebase.php"; // Firebase Connection

$errorMessage = "";
$itemId = $name = $price = $description = $imageBase64 = $Searchname = $dbcategory = $dbseason = $stock = "";
$showFormElements = false;

// Fetch the item based on the search name
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
                    $stock = $item['stock'];

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

// Update the item based on form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["itemname"])) {
    if (isset($_SESSION['itemId'])) {
        $itemId = $_SESSION['itemId'];
    } else {
        $errorMessage = "Error: No item ID found in the session.";
        exit();
    }

    $name = $_POST['itemname'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $season = $_POST['season'];
    $stock = $_POST['stock'];

    // 🔥 Check if both category and season are nil
    if ($category === "nil" && $season === "nil") {
        $errorMessage = "Error: Please select either a category or a season.";
        $showFormElements = true;
    } else {
        // Reset season or category to 'nil' as appropriate
        if ($category !== "nil") {
            $season = "nil";
        } elseif ($season !== "nil") {
            $category = "nil";
        }

        $updateData = [
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'category' => $category,
            'season' => $season,
            'stock' => $stock
        ];

        // Preserve existing image if no new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $fileTempPath = $_FILES['image']['tmp_name'];
            $imageData = file_get_contents($fileTempPath);
        if ($imageData !== false) {
            $base64Image = base64_encode($imageData);
            $updateData['imageBase64'] = $base64Image; // 🔥 Update Base64 image
        } else {
            $errorMessage = "Failed to read the uploaded image.";
            $showFormElements = true;
        }
    } else {
        // 🔥 Keep existing image if no new file is uploaded
        if (isset($_POST['existingImageBase64']) && !empty($_POST['existingImageBase64'])) {
            $imageBase64 = $_POST['existingImageBase64'];
        }
    }

        if (empty($errorMessage)) {
            // 🔥 Update item in Firestore
            $database->getReference("menu/{$itemId}")->update($updateData);
            $_SESSION['message'] = "Item updated successfully!";
        } else{
            echo "<p style='color: red;'>$errorMessage</p>";
            $showFormElements = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Menu Item</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admineditmenu.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Update Menu Item</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="Searchname" class="form-label">Enter Menu Item Name:</label>
                <input type="text" name="Searchname" id="Searchname" class="form-control" value="<?php echo htmlspecialchars($Searchname); ?>" placeholder="Search for an item" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Search</button>
            <p id="result" style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        </form>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
            <div class="<?php echo $showFormElements ? '' : 'hidden-elements'; ?>">
                <div class="mb-3">
                    <label for="category" class="form-label">Category:</label>
                    <select name="category" id="category" class="form-select">
                        <option value="nil" <?php echo ($dbcategory === 'nil' ? 'selected' : ''); ?>>Nil</option>
                        <option value="set" <?php echo ($dbcategory === 'set' ? 'selected' : ''); ?>>Set</option>
                        <option value="alacarte" <?php echo ($dbcategory === 'alacarte' ? 'selected' : ''); ?>>Ala-carte</option>
                        <option value="sides" <?php echo ($dbcategory === 'sides' ? 'selected' : ''); ?>>Sides</option>
                        <option value="beverages" <?php echo ($dbcategory === 'beverages' ? 'selected' : ''); ?>>Beverages</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="season" class="form-label">Season:</label>
                    <select name="season" id="season" class="form-select">
                        <option value="nil" <?php echo ($dbseason === 'nil' ? 'selected' : ''); ?>>Nil</option>
                        <option value="CNY" <?php echo ($dbseason === 'CNY' ? 'selected' : ''); ?>>Chinese New Year</option>
                        <option value="hari_raya" <?php echo ($dbseason === 'hari_raya' ? 'selected' : ''); ?>>Hari Raya</option>
                        <option value="deepavali" <?php echo ($dbseason === 'deepavali' ? 'selected' : ''); ?>>Deepavali</option>
                        <option value="halloween" <?php echo ($dbseason === 'halloween' ? 'selected' : ''); ?>>Halloween</option>
                        <option value="xmas" <?php echo ($dbseason === 'xmas' ? 'selected' : ''); ?>>Christmas</option>
                        <option value="vday" <?php echo ($dbseason === 'vday' ? 'selected' : ''); ?>>Valentine</option>
                        <option value="lantern" <?php echo ($dbseason === 'lantern' ? 'selected' : ''); ?>>Mid-Autumn Festival</option>
                    </select>
                </div>

                <input type="hidden" name="existingImageBase64" value="<?php echo htmlspecialchars($imageBase64); ?>">
                <?php 
                // Ensure the imageBase64 is available even after an error
                if (isset($_POST['existingImageBase64']) && !empty($_POST['existingImageBase64'])) {
                    $imageBase64 = $_POST['existingImageBase64'];
                }
                ?>

                <?php if (!empty($imageBase64)): ?>
                <div class="mb-3 text-center">
                    <label class="form-label">Current Image:</label>
                    <div>
                        <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($imageBase64); ?>" 
                        alt="Current Image" 
                        class="img-thumbnail" 
                        style="max-height: 150px;">
                    </div>
                </div>
                <?php endif; ?>


                <div class="mb-3">
                    <label for="image" class="form-label">New Image:</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="itemname" class="form-label">Name of Item:</label>
                    <input type="text" name="itemname" id="itemname" class="form-control" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter item name" required>
                </div>
            
                <div class="mb-3">
                    <label for="price" class="form-label">Price:</label>
                    <input type="text" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" placeholder="Enter item price" required>
                </div>
            
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter item description"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock:</label>
                    <select name="stock" id="stock" class="form-select">
                        <option value="For Sale" <?php echo $stock == 'For Sale' ? 'selected' : ''; ?>>For Sale</option>
                        <option value="Out of Stock" <?php echo $stock == 'Out of Stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </div>
        </form>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('category');
        const seasonSelect = document.getElementById('season');

        // Add a change event listener to the Category dropdown
        categorySelect.addEventListener('change', function () {
            if (categorySelect.value !== "nil") {
                seasonSelect.value = "nil"; // Reset Season dropdown to 'nil'
            }
        });

        // Add a change event listener to the Season dropdown
        seasonSelect.addEventListener('change', function () {
            if (seasonSelect.value !== "nil") {
                categorySelect.value = "nil"; // Reset Category dropdown to 'nil'
            }
        });
    });
</script>

<div class="text-center mt-4">
    <a href="AdminMain.php" class="btn btn-primary">Back to Dashboard</a>
</div>

<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
