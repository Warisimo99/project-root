<?php
session_start();
$resolved_path = __DIR__ . '/../db.php';
error_log("Resolved path: ".$resolved_path);
error_log("File exists:".(file_exists($resolved_path) ? 'Yes' : 'No'));
require_once $resolved_path;

// Check if the connection was successful
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

$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (!is_array($posts)) {
    $posts = [];
}

foreach ($posts as &$post) {
    if (!empty($post['image'])) {
        $post['image_url'] = '/assets/images/' . rawurlencode(basename($post['image']));
    }
    // No else clause for default images
    else {
        $post['image'] = '/assets/images/';
    }
    
    // Format other post data
    $post['formatted_date'] = date('F j, Y', strtotime($post['created_at']));
    $post['excerpt'] = substr($post['content'], 0, 150) . (strlen($post['content']) > 150 ? '...' : '');
}

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);
?>