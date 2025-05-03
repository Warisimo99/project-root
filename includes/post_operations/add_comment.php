<?php

session_start();
// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db.php';

// Initialize HTML Purifier
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 'p,br,a[href],strong,em,ul,ol,li,img[src|alt],h1,h2,h3,h4');
$purifier = new HTMLPurifier($config);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to comment.");
    }

    // Validate the post ID
    if (!isset($_POST['post_id'])) {
        die("Post ID is missing.");
    }

    $postId = intval($_POST['post_id']);
    $userId = intval($_SESSION['user_id']);
    $comment = $purifier->purify(trim($_POST['comment'])); 

    if (empty($comment)) {
        die("Comment cannot be empty.");
    }

    $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iis', $postId, $userId, $comment);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        // Redirect back to the post page
        header("Location: public/read.php?id=$postId");
        exit();
    } else {
        die("Failed to add comment: " . mysqli_error($conn));
    }
} else {
    die("Invalid request method.");
}
?>