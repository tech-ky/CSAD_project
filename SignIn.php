<html lang="en">
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link href="css/UserSignIn.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container" id="AdminSignIn" style="display:none">
            <h1 class="form-title">Admin</h1>
            <form method="post" action="">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" id="name" placeholder="Name">
                    <label for="name">Name</label>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password">
                    <label for="password">Password</label>
                </div>  

                <div class="button-group">
                    <button id="back_button"><i class="fas fa-arrow-left"></i> Back</button>
                    <button id="go_button" type="button">Go <i class="fas fa-arrow-right"></i></button>
                </div>

                <p id="error-message" style="color: red;"></p>
            </form>
        </div>

        <div class="container" id="SignInBY">
            <h1 class="form-title">Sign In</h1>
            <form>
                <div class="button-group">
                    <a href="Home.php"><button id="user_button" type="button"><i class="fas fa-user"></i> User</button></a>
                    <button id="admin_button" type="button"><i class="fas fa-hard-hat"></i> Admin</button>
                 </div>
            </form>
        </div>

        <script>
            const GoButton = document.getElementById('go_button');
            const BackButton = document.getElementById('back_button');
            const AdminButton = document.getElementById('admin_button');
            const UserButton = document.getElementById('user_button');
            const SignInBY = document.getElementById('SignInBY');
            const AdminSignIn = document.getElementById('AdminSignIn');
            const errorMessage = document.getElementById("error-message");

            AdminButton.addEventListener('click', function() {
                AdminSignIn.style.display = "block";
                SignInBY.style.display = "none"; 
            });

            BackButton.addEventListener('click', function() {
                AdminSignIn.style.display = "none";
                SignInBY.style.display = "block"; 
            });

            GoButton.addEventListener('click', function() {
                const validUsername = "1234";
                const validPassword = "1234";
                const Username = document.getElementById("name").value;
                const Password = document.getElementById("password").value;
                
                if (Username === "" || Password === "") {
                    errorMessage.innerHTML = "Please fill in the required fields";
                } else if (Username === validUsername && Password === validPassword) {
                    window.location.href = "Adminmain.php";  
                } else {
                    errorMessage.innerHTML = "Invalid Username/Password";
                }
            });
        </script>
    </body>
</html>