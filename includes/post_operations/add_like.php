<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to like a post.");
    }

    $postId = intval($_POST['post_id']);
    $userId = intval($_SESSION['user_id']); 

    // Check if the user has already liked the post
    $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $postId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // User has already liked the post
        $_SESSION['error'] = "You have already liked this post.";
    } else {
        // Insert the like into the database
        $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $postId, $userId);
        mysqli_stmt_execute($stmt);
    }

    // Redirect back to the post page
    header("Location: read.php?id=$postId");
    exit();
}
?>