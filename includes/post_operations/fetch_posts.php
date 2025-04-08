<?php

session_start();
$resolved_path = __DIR__ . '/../db.php';
error_log("Resolved path: ".$resolved_path);
error_log("File exists:".(file_exists($resolved_path) ? 'Yes' : 'No'));
require_once $resolved_path;


$posts = [];

// Check if the connection was successful
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch posts from the database
$sql = "SELECT id, title, image FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($posts) {
    foreach ($posts as &$post) {
        $post['image_url'] = 'includes/public/images/' . $post['image'];
    }
}

// Fetch all rows as an associative array
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);
?>