<?php
include "firebase.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $category = $_POST['category'];
    $season = $_POST['season'];
    $itemname = trim($_POST['itemname']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $stock = "For Sale";
    $errorMessage = "";

    // Check if both category and season are 'nil'
    if ($category === "nil" && $season === "nil") {
        $errorMessage = "Please select either a category or a season.";
    }

    if (empty($errorMessage)) {
        // Define target directory (for organizational purposes, not used for Firebase)
        if ($category !== "nil") {
            $targetDir = "uploads/$category/"; 
        } elseif ($season !== "nil") {
            $targetDir = "uploads/season/$season/";
        }

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $photoname = $_FILES['photo']['name'];
        $fileTempPath = $_FILES['photo']['tmp_name'];
        $fileType = strtolower(pathinfo($photoname, PATHINFO_EXTENSION)); // 🔥 FIXED

        // Allowed file types
        $allowedTypes = array("jpg", "jpeg", "png", "gif");

        if (in_array($fileType, $allowedTypes)) {
            // Read image file and convert to Base64
            $imageData = file_get_contents($fileTempPath);
            if ($imageData === false) {
                $errorMessage = "Failed to read the image file.";
            } else {
                $base64Image = base64_encode($imageData);

                // Check if the item name already exists in Firestore
                $menuRef = $database->getReference('menu')->getValue();
                $exists = false;
                if ($menuRef) {
                    foreach ($menuRef as $key => $item) {
                        if ($item['name'] === $itemname) {
                            $exists = true;
                            break;
                        }
                    }
                }

                if ($exists) {
                    $errorMessage = "The item name already exists. Please choose a unique name.";
                } else {
                    // Store item details in Firestore with Base64-encoded image
                    $database->getReference('menu')->push([
                        'category' => $category,
                        'season' => $season,
                        'name' => $itemname,
                        'price' => $price,
                        'description' => $description,
                        'imageBase64' => $base64Image, // 🔥 Store image as Base64
                        'stock' => $stock
                    ]);

                    echo "<p>Item added successfully!</p>";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
        } else {
            $errorMessage = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Display error messages
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/AdminAddMenu.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="form-container">
    <h2>Add Item</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label">Category:</label>
            <select id="category" name="category" class="form-select" required>
                <option value="nil" <?php echo (isset($_POST['category']) && $_POST['category'] == "nil") ? 'selected' : ''; ?>>Nil</option>
            <option value="set" <?php echo (isset($_POST['category']) && $_POST['category'] == "set") ? 'selected' : ''; ?>>Set</option>
            <option value="alacarte" <?php echo (isset($_POST['category']) && $_POST['category'] == "alacarte") ? 'selected' : ''; ?>>Ala-carte</option>
            <option value="sides" <?php echo (isset($_POST['category']) && $_POST['category'] == "sides") ? 'selected' : ''; ?>>Sides</option>
            <option value="beverages" <?php echo (isset($_POST['category']) && $_POST['category'] == "beverages") ? 'selected' : ''; ?>>Beverages</option>
            </select>
        </div>
        
        <!-- Season Selection -->
        <div class="mb-3">
            <label for="category" class="form-label">Season:</label>
            <select id="season" name="season" class="form-select" required>
                <option value="nil" <?php echo (isset($_POST['season']) && $_POST['season'] == "nil") ? 'selected' : ''; ?>>Nil</option>
                <option value="CNY" <?php echo (isset($_POST['season']) && $_POST['season'] == "CNY") ? 'selected' : ''; ?>>Chinese New Year</option>
                <option value="hari_raya" <?php echo (isset($_POST['season']) && $_POST['season'] == "hari_raya") ? 'selected' : ''; ?>>Hari Raya</option>
                <option value="deepavali" <?php echo (isset($_POST['season']) && $_POST['season'] == "deepavali") ? 'selected' : ''; ?>>Deepavali</option>
                <option value="halloween" <?php echo (isset($_POST['season']) && $_POST['season'] == "halloween") ? 'selected' : ''; ?>>Halloween</option>
                <option value="xmas" <?php echo (isset($_POST['season']) && $_POST['season'] == "xmas") ? 'selected' : ''; ?>>Christmas</option>
                <option value="vday" <?php echo (isset($_POST['season']) && $_POST['season'] == 'vday') ? 'selected' : ''; ?>>Valentine's</option>
                <option value="lantern" <?php echo (isset($_POST['season']) && $_POST['season'] == 'lantern') ? 'selected' : ''; ?>>Mid-Autumn Festival</option>
            </select>
        </div>
        
        <!-- Name of Item -->
            <div class="mb-3">
                <label for="itemname" class="form-label">Name of Item:</label>
                <input type="text" id="itemname" name="itemname" class="form-control" value="<?php echo isset($_POST['itemname']) ? htmlspecialchars($_POST['itemname']) : ''; ?>" placeholder="Enter item name" required>
            </div>
        
        <!-- Price -->
        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="text" id="price" name="price" class="form-control" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" placeholder="Enter price" required>
        </div>
        
        
        <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
        
        <!-- Image -->
        <div class="mb-3">
            <label for="image" class="form-label">Image:</label>
            <input type="file" id="image" name="photo" class="form-control" accept="image/*" required>
            <img id="imagePreview" alt="Selected Image Preview">
        </div>
        
        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" name="add" class="btn btn-primary">Add</button>
        </div>
    </form>
    
    <!-- JavaScript for Image Preview -->
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const imagePreview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result; // Set the preview image's src to the file's data URL
                    imagePreview.style.display = 'block'; // Make the image preview visible
                };
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                imagePreview.style.display = 'none'; // Hide the preview if no file is selected
            }
        });
    </script>
    
    <script>
    // Get the select elements
    const categorySelect = document.getElementById('category');
    const seasonSelect = document.getElementById('season');

    // Add event listeners to both select elements
    categorySelect.addEventListener('change', function () {
        if (categorySelect.value !== "nil") {
            // If a category is selected, set season to 'nil'
            seasonSelect.value = "nil";
        }
    });

    seasonSelect.addEventListener('change', function () {
        if (seasonSelect.value !== "nil") {
            // If a season is selected, set category to 'nil'
            categorySelect.value = "nil";
        }
    });
    </script>
    </div>
    <div class="text-center mt-4">
        <a href="AdminMain.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>