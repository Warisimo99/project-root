<?php

require_once(__DIR__.'includes/db.php'); 

//errors from login.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" id="signup" style="display:none;">
          <!-- Display success/error messages -->
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <h1 class= "form-title">Register</h1>
        <form method="post" action="/includes/user/login.php" id= "signup" > 
            <div class= "input-group">
                <input type="text" name="fname" id="fname" placeholder="First Name" required>
                <label for="fname">First Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="lname" id="lname"  placeholder="Last Name" required>
                <label for="lname">Last Name</label>
            </div>
            <div class="input-group">
                <input type="email" name="email" id="email"  placeholder="Email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password"  placeholder="password" required>
                <label for="password">Password</label>
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password"  placeholder="Confirm password" required>
                <label for="Confirm password">Confirm Password</label>
            </div>

            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
            <p>Already Have Account?</p>
            <button id="signInButton">Sign In</button>
        </div>
    </div>

    <div class="container" id="signin">
        <h1 class= "form-title">Sign In</h1>
        <form method="post" action="includes/user/login.php" id= "signin" > 
            
        <div class="input-group">
                <input type="email" name="email" id="email"  placeholder="Email" required>
                <label for="email">Email</label>
        </div>

        <div class="input-group">
                <input type="password" name="password" id="password"  placeholder="password" required>
                <label for="password">Password</label>
        </div>

            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
            <p>Dont have account yet?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>

    
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>


    <script type="module" src ="assets/js/main.js"></script>

</body>
</html>