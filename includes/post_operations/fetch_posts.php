<?php
session_start();
require_once __DIR__ . '/../db.php';

// Check connection
if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch posts
$sql = "SELECT id, title, image, content, created_at FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$posts = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: []; // Simplified empty check

foreach ($posts as &$post) {
    // Image URL handling
    if (!empty($post['image'])) {
        // Ensure consistent path format
        $post['image'] = 'public/assets/images/' . htmlspecialchars(basename($post['image']));

        
        // Debug logging
        error_log("Image path for post {$post['id']}: {$post['image']}");
    }
    // Remove else clause completely if you don't want default images
    // (Your current else was setting an invalid path)
    
    // Post formatting
    $post['formatted_date'] = date('F j, Y', strtotime($post['created_at']));
    $post['excerpt'] = substr($post['content'], 0, 150) . (strlen($post['content']) > 150 ? '...' : '');
}

header('Content-Type: application/json');
echo json_encode($posts);
?>