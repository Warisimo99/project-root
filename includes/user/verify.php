<?php
session_start();

require_once __DIR__ . '/../db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Mark the user as verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $row['id']);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Email verified successfully! You can now log in.";
        } else {
            $_SESSION['error'] = "Failed to verify email.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid or expired verification token.";
    }

    // Redirect to login page
    header("Location: /../public/register.php");
    exit();
} else {
    $_SESSION['error'] = "No verification token provided.";
    header("Location: /../public/register.php");
    exit();
}
?>