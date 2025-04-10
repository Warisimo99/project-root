<?php
session_start();
$resolved_path = __DIR__ . '/../db.php';
error_log("Resolved path: ".$resolved_path);
error_log("File exists:".(file_exists($resolved_path) ? 'Yes' : 'No'));
require_once $resolved_path;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

// Check if the post ID is provided
if (!isset($_GET['id'])) {
    header("Location: public/dashboard.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Delete the post
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "Post deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete post.";
}

header("Location: public/post.php");
exit();
?>