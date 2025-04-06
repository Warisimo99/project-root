<?php
session_start();
include('/db.php'); // Include your database connection file

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the post ID is provided
if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the post to edit
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
} else {
    echo "Post not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']); // Get the category field

    // Validate input
    if (empty($title) || empty($content) || empty($category)) {
        $_SESSION['error'] = "Title, content, and category cannot be empty.";
        header("Location: update_post.php?id=$post_id");
        exit();
    }

    // Update the post in the database
    $update_stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category = ? WHERE id = ? AND user_id = ?");
    if (!$update_stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $update_stmt->bind_param("sssii", $title, $content, $category, $post_id, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Post updated successfully.";
        header("Location: profile.php"); // Redirect to profile or dashboard
        exit();
    } else {
        $_SESSION['error'] = "Failed to update post.";
        header("Location: update_post.php?id=$post_id");
        exit();
    }
}
?>