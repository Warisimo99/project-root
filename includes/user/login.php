<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();


$db_path = __DIR__ . '/../../includes/db.php';
error_log("looking at: . $db_path");

if(!file_exists($db_path)) {
    die(json_encode([
        'error' => 'Database config missing',
        'debug' => [
            'current_dir' => __DIR__,
            'attempted_path' => $db_path,
            'file_exists' => file_exists($db_path)
        ]
        ]));
}

require_once $db_path;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//require __DIR__ . '/path/to/PHPMailer/src/Exception.php';
//require __DIR__ . '/path/to/PHPMailer/src/PHPMailer.php';
//require __DIR__ . '/path/to/PHPMailer/src/SMTP.php';
require __DIR__ . '../../vendor/autoload.php';
echo __DIR__ . '../../vendor/autoload.php';


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

        // Send the email
        $mail->send();
        return true; 
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage()); 
        return false;
    }
}

if (isset($_POST['signUp'])) {
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

            // Generate a verification token
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
    header("Location: public/register.php");
    exit();
} elseif (isset($_POST['signIn'])) {
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

                    // Redirect to dashboard
                    header("Location: /../public/dashboard.php");
                    //$_SESSION['success'] = "Registration successful! Your email has been confirmed.";
                    exit();
                } else {
                    // Email is not verified - just show error without redirect
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
?>