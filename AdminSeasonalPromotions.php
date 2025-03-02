<?php
include "firebase.php"; // Firebase Connection

$currentSeason = "nil"; // Default value
$message = "";

// 🔥 Fetch current season from Firestore
$seasonRef = $database->getReference('settings/season')->getValue();
if ($seasonRef !== null) {
    $currentSeason = $seasonRef;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['season'])) {
    $selectedSeason = $_POST['season'];

    // 🔥 Update selected season in Firestore
    $database->getReference('settings')->update(['season' => $selectedSeason]);

    $message = "Seasonal promotion updated successfully!";
    $currentSeason = $selectedSeason;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Seasonal Promotions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/AdminSeasonalPromotion.css" rel="stylesheet" type="text/css">
   
</head>
<body>
    <div class="container">
        <h1>Change Seasonal Promotion</h1>

        <form method="POST" action="">
            <div class="form-group">
                <label for="season">Select Season to Display on Homepage:</label>
                <select name="season" id="season">
                    <option value="nil" <?php echo $currentSeason == 'nil' ? 'selected' : ''; ?>>No Seasonal Promotion</option>
                    <option value="CNY" <?php echo $currentSeason == 'CNY' ? 'selected' : ''; ?>>Chinese New Year</option>
                    <option value="hari_raya" <?php echo $currentSeason == 'hari_raya' ? 'selected' : ''; ?>>Hari Raya</option>
                    <option value="deepavali" <?php echo $currentSeason == 'deepavali' ? 'selected' : ''; ?>>Deepavali</option>
                    <option value="halloween" <?php echo $currentSeason == 'halloween' ? 'selected' : ''; ?>>Halloween</option>
                    <option value="xmas" <?php echo $currentSeason == 'xmas' ? 'selected' : ''; ?>>Christmas</option>
                    <option value="vday" <?php echo $currentSeason == 'vday' ? 'selected' : ''; ?>>Valentine's</option>
                    <option value="lantern" <?php echo $currentSeason == 'lantern' ? 'selected' : ''; ?>>Mid-Autumn Festival</option>
                </select>
            </div>

            <button type="submit">Update Season</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="AdminMain.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
