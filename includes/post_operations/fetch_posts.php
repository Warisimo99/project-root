<?php

session_start();

require_once(__DIR__.'project-root/includes/db.php'); 

// Check if the connection was successful
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch posts from the database
$sql = "SELECT id, title, image FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);


foreach ($posts as &$post) {
    $post['image_url'] = 'includes/public/images/' . $post['image'];
}

if (!$result) {
    die(json_encode(['error' => 'SQL query failed: ' . mysqli_error($conn)]));
}

// Fetch all rows as an associative array
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);
?>