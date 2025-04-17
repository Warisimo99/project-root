<?php
// Start the session
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db.php';

/**
 * Generates and stores a new CSRF token with expiration
 */
function generateCsrfToken() {
    // Generate new token and set expiration (15 minutes)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 900;
    return $_SESSION['csrf_token'];
}

/**
 * Validates the CSRF token
 * @return bool True if valid, false otherwise
 */
function validateCsrfToken($token) {
    // Check if token exists and matches
    if (empty($token) || 
        empty($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    
    // Check expiration
    if (empty($_SESSION['csrf_token_expiry']) || 
        time() > $_SESSION['csrf_token_expiry']) {
        return false;
    }
    
    // Token is valid - generate a new one for next request
    generateCsrfToken();
    return true;
}

// Generate a new CSRF token if one doesn't exist or has expired
if (empty($_SESSION['csrf_token']) || 
    (isset($_SESSION['csrf_token_expiry']) && time() > $_SESSION['csrf_token_expiry'])) {
    generateCsrfToken();
}

if (isset($_POST['signUp'])) {
    // CSRF validation
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security token invalid or expired";
        header("Location: /../public/register.php");
        exit();
    }

    // Handle Sign Up
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Email already exists. Please use a different email.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $verification_token = bin2hex(random_bytes(32));
            
            $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password, verification_token, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $fname, $lname, $email, $hashedPassword, $verification_token);

            if ($stmt->execute()) {
                // Send verification email
                if (sendemail_verify($fname, $lname, $email, $verification_token)) {
                    $_SESSION['success'] = "Registration successful! Please check your email to verify your account.";
                } else {
                    $_SESSION['error'] = "Registration successful, but failed to send verification email.";
                }
            } else {
                $_SESSION['error'] = "Error: Unable to register user. Please try again.";
            }
            $stmt->close();
        }
    }

    // Redirect back to register.php
    header("Location: /../public/register.php");
    exit();
} elseif (isset($_POST['signIn'])) {
    // CSRF validation
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security token invalid or expired";
        header("Location: /../public/register.php");
        exit();
    }

    // Handle Sign In
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Check if the user's email is verified
                if ($row['is_verified'] == 1) {
                    // Email is verified, log the user in
                    $_SESSION['user_id'] = $row['id']; 
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['fname'] = $row['fname'];
                    $_SESSION['lname'] = $row['lname'];

                    // Generate a new CSRF token for the authenticated session
                    generateCsrfToken();

                    // Redirect to dashboard
                    header("Location: /../public/dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Please verify your email before logging in.";
                }
            } else {
                $_SESSION['error'] = "Incorrect password.";
            }
        } else {
            $_SESSION['error'] = "User not found.";
        }
        $stmt->close();
    }
    
    header("Location: /../public/register.php");
    exit(); 
}

// PHPMailer implementation remains the same
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoloadPath = file_exists(__DIR__ . '/vendor/autoload.php') 
    ? __DIR__ . '/vendor/autoload.php' : '/app/vendor/autoload.php';            
require $autoloadPath;

function sendemail_verify($fname, $lname, $email, $verify_token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'auth099109@gmail.com'; 
        $mail->Password = 'jajp rxqr styh zxgr'; 
        $mail->SMTPSecure = 'tls'; 
        $mail->Port = 587; 

        $mail->setFrom('auth099109@gmail.com', $fname);
        $mail->addAddress($email); 

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $verification_link = "http://shielded-garden-06013-bc1acbc5ebcd.herokuapp.com/includes/user/verify.php?token=$verify_token";
        $mail->Body = "
            <h2>Hello $fname $lname,</h2>
            <p>Thank you for registering! Please click the link below to verify your email address:</p>
            <p><a href='$verification_link'>Verify Email</a></p>
            <p>If you did not create an account, you can safely ignore this email.</p>
        ";

        $mail->send();
        return true; 
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage()); 
        return false;
    }
}
?>