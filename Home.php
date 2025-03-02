<?php
include "firebase.php"; // Firebase Connection

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);
$username = $isLoggedIn ? $_SESSION['user'] : '';

// 🔥 Fetch current season from Firestore
$currentSeason = $database->getReference('settings/season')->getValue();
if ($currentSeason === null) {
    $currentSeason = "nil";
}

// 🔥 Fetch photos for the current season from Firestore
$photos = [];
$menuItems = $database->getReference('menu')->getValue();
if ($menuItems) {
    foreach ($menuItems as $item) {
        if (isset($item['season']) && $item['season'] === $currentSeason) {
            $photos[] = $item['imageBase64'] ?? ""; // Base64 Image
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/topnavigation.css" rel="stylesheet" type="text/css">
    <link href="css/Home.css" rel="stylesheet" type="text/css">
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
                <li><a href="Home.php" class='btn btn-primary' style='color: white'>Home</a></li>
                <li><a href="Menu.php" class='btn btn-outline-primary'>Menu</a></li>
                <li><a href="Location.php" class='btn btn-outline-primary'>Locations</a></li>
            </ul>
        </div>
    </nav>
    
    <!--Advertisement-->
    <header class="OrderNow">
        <div>
            <h1>Peter's Adode</h1>
            <h2>At our restaurant, we take pride in serving <span class="highlight">Fresh</span>, high-quality ingredients in every dish. Order Now!!</h2>
            <a href="Menu.php"><button>Order Now</button></a>
        </div>
    </header>

<section class="SeasonalPromotion">
    <div class="horizontal-scroll" id="scrollContainer">
        <?php if ($currentSeason !== 'nil'): ?>
            <?php 
                // Define the folder for the current season
                $seasonalPosters = [
                    "CNY" => "seasonalCNYPictures",
                    "xmas" => "seasonalCMPictures",
                    "vday" => "seasonalVictures",
                    "hari_raya" => "seasonalHRPictures",
                    "deepavali" => "seasonalDPictures",
                    "halloween" => "seasonalHPictures",
                    "lantern" => "seasonalMAFPictures"
                ];

                if (isset($seasonalPosters[$currentSeason])) {
                    $folderPath = $seasonalPosters[$currentSeason];
                    $imageFiles = scandir($folderPath);
                    $count = 0;
                    foreach ($imageFiles as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $imageURL = $folderPath . '/' . $file;
                            echo "<img src='{$imageURL}' alt='Seasonal Promotion' class='carousel-image'>";
                            $count++;
                        }
                    }
                }
            ?>
            <!-- Navigation Dots -->
            <div class="dot-container">
                <?php 
                    $count = 0;
                    foreach ($imageFiles as $file) { 
                        if ($file !== '.' && $file !== '..') {
                            echo "<a href='#slide{$count}' class='dot'></a>";
                            $count++;
                        }
                    }
                ?>
            </div>
        </div>
        <?php else: ?>
            <p class="message">No promotions available for the current season.</p>
        <?php endif; ?>
</section>

    <script>
        const scrollContainer = document.getElementById('scrollContainer');
        const dots = document.querySelectorAll('.dot');

        function updateActiveDot(index) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        scrollContainer.addEventListener('scroll', () => {
            const width = scrollContainer.children[0].offsetWidth;
            const index = Math.round(scrollContainer.scrollLeft / width);
            updateActiveDot(index);
        });

        if (dots.length > 0) {
            dots[0].classList.add('active');
        }
    </script>
    
        <section class="AboutUs">
    <table>
        <tr>
            <td>
                <div class="text">
                <h1 >Meet Peter: The Passion Behind the Restaurant</h1>
                <p>Peter’s love for food and hospitality started from his childhood kitchen, watching his grandmother cook hearty meals. He always dreamed of opening a restaurant that brings people together through delicious, high-quality dishes.</p>          
                </div>
            </td>
            <td>
                <div>
                    <img src="HomePictures/peter.jpg" alt="Peter in the kitchen">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <img src="HomePictures/vegtables.jpg" alt="Peter in the kitchen">
                </div>
            </td>
            <td>
                <div class="text">
                    <h1>Our Philosophy: Quality & Freshness</h1>
                    <p>At Peter’s Abode, we believe in using only the freshest ingredients sourced from local farmers. Every dish is crafted with passion and precision to ensure an unforgettable dining experience.</p> 
               
                </div>   
            </td>
        </tr>
        <tr>
            <td>
                <div class="text">
                    <h1>A Cozy Spot for Everyone</h1>
                    <p>Whether you're here for a quick bite, a family dinner, or a celebration, our warm and inviting atmosphere makes every visit special. We want our restaurant to feel like home.</p> 
                 </div>
            </td>
            <td>
                <div>
                    <img src="HomePictures/eating.jpg" alt="Cozy dining area">
                </div>
            </td>
        </tr>
    </table>
</section>
<footer>
    <table style="width: 100%; text-align: center; font-family: Calibri;">
        <tr>
            <td>
                <h3 class="text-lg font-bold">Quick Links</h3>
                <ul class="list-disc pl-5 text-muted-foreground">
                    <li>Delivery & Self Pick Up</li>
                    <li>View Menu</li>
                    <li>Promotions</li>
                    <li>Reserve A Table</li>
                    <li>Franchise Opportunities</li>
                </ul>
            </td>
            <td>
                <h3 class="text-lg font-bold">Contact Information</h3>
                <ul class="text-muted-foreground">
                    <li>Dover: 9022 5452</li>
                    <li>Woodlands: 8775 2831</li>
                    <li>Email: <a href="mailto:.com" class="text-accent">peter@gmail.com</a></li>
                </ul>
            </td>
            <td>
                <h3 class="text-lg font-bold">Follow Our Socials</h3>
                <div class="flex space-x-4 mt-2">
                    <a href="#" class="text-primary"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="text-primary"><i class="fa fa-instagram"></i></a>
                </div>
            </td>
        </tr>
    </table>
</footer>

  
</body>
</html>
