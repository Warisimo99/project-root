<?php
session_start();
require_once('/db.php');

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

    // Sanitize inputs
    $postId = intval($_POST['post_id']);
    $userId = intval($_SESSION['user_id']);
    $comment = trim($_POST['comment']); 

    // Validate the comment
    if (empty($comment)) {
        die("Comment cannot be empty.");
    }

    // Insert the comment into the database
    $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iis', $postId, $userId, $comment);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        // Redirect back to the post page
        header("Location: read.php?id=$postId");
        exit();
    } else {
        die("Failed to add comment: " . mysqli_error($conn));
    }
} else {
    die("Invalid request method.");
}
?>