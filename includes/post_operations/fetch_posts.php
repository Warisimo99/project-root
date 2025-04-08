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
    // Add null check for $post['image']
    if (!empty($post['image'])) {
        $filename = basename($post['image']);
        $post['image_url'] = '/assets/images/' . basename($post['image']);
        
        // Debugging - verify the path
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/' . basename($post['image']);
        error_log("Checking image at: " . $absolutePath);
        error_log("Image exists: " . (file_exists($absolutePath) ? 'Yes' : 'No'));
    } else {
        $post['image_url'] = ''; // or a default image
        error_log("Empty image path for post ID: " . $post['id']);
    }

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);

}
?>