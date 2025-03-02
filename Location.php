<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link href="css/topnavigation.css" rel="stylesheet" type="text/css">
     <link href="css/location.css" rel="stylesheet" type="text/css">
     <style>
         @import url('https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap');
         .card{
             background-color: transparent;
             font-family: "Permanent Marker", serif;
         }
        .body{
            background-image: url('HomePictures/background.jpg');
        }
        
     </style>
</head>
<body style="background-image: url('HomePictures/background.jpg');">
    
 
    <!-- Top Navigation -->
    <nav class="navbar">
    <div class="top-nav">
        <ul>
            <li><img src="HomePictures/logo.png" alt="Logo"></li>
            <li><button onclick="window.location.href='SignIn.php'">Sign In</button></li>
        </ul>
        <ul>
            <li><a href="Home.php" class='btn btn-outline-primary'>Home</a></li>
            <li><a href="Menu.php" class='btn btn-outline-primary'>Menu</a></li>
            <li><a href="Location.php" class='btn btn-primary' style='color: white'>Locations</a></li>
        </ul>
    </div>
    </nav>
    
    <!-- Card Container -->
    <div class="card-container">
        
        <!-- Card 1 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc1.png">
                    <h3>VivoCity</h3>
                    <p>1 HarbourFront Walk, Singapore 098585, L2-01</p>
                </div>
                <div class="card-back">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8463683434725!2d103.82007777494218!3d1.2647139118436481!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da1958974bbc2b%3A0xba34578f519df13f!2sVivoCity!5e0!3m2!1sen!2ssg!4v1738409956646!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc2.jpeg">
                    <h3>Dover</h3>
                    <p>200 Commonwealth Ave W, Singapore 138677, L1-02</p>
                </div>
                <div class="card-back">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.782267039457!2d103.80832007494203!3d1.3057544617160555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da1a22d9c8acf5%3A0xe27309ad5a1f4ac!2sDover%20Street%20Market%20Singapore!5e0!3m2!1sen!2ssg!4v1738408594083!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
        
        <!-- Card 3 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc3.jpeg" alt="Woodlands">
                    <h3>Woodlands</h3>
                    <p>1 Woodlands Square, Singapore 738099, B1-03</p>
                </div>
                <div class="card-back">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.5651108518014!2d103.78107617864723!3d1.4360986575784618!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da13a8bd844883%3A0x4f271a68efda9c2e!2sCauseway%20Point!5e0!3m2!1sen!2ssg!4v1738408533376!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
      
        <!-- Card 4 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc4.jpeg">
                    <h3>Waterway Point</h3>
                    <p>83 Punggol Central, Singapore 828761 Waterway Point, B2-04</p>
                </div>
                <div class="card-back">
                     <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.6163348904306!2d103.89950967496588!3d1.406441198580292!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da15e4a68f302d%3A0x31aca96bff05c660!2sWaterway%20Point!5e0!3m2!1sen!2ssg!4v1738408472118!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
        
        <!-- Card 5 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc5.png">
                    <h3>Bugis+</h3>
                    <p>100 Bugis Plus, Singapore 808061 , L1-05</p>
                </div>
                <div class="card-back">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127641.3445520152!2d103.70178379726563!3d1.2995955000000246!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da19baef4923c1%3A0xd2dad49697017fb9!2sBugis%2B!5e0!3m2!1sen!2ssg!4v1738414643863!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
                
         <!-- Card 6 -->
        <div class="card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="location/loc6.png">
                    <h3>Hitlon Orchard</h3>
                    <p>83 Hitlon Mall, Singapore 433761 central Orchard, A2-06</p>
                </div>
                <div class="card-back">
                     <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.788342292544!2d103.83329237496574!3d1.301920298685721!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da19b77cf36053%3A0x5c34ea499c8061e1!2sHilton%20Singapore%20Orchard!5e0!3m2!1sen!2ssg!4v1738415126450!5m2!1sen!2ssg" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>              </div>
            </div>
        </div>
   
    </div>

    <script>
        function flipCard(card) {
            card.classList.toggle('flipped');
        }
    </script>
</html>
</body>