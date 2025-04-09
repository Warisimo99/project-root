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
        // 1. Get clean filename and encode special characters
        $filename = rawurlencode(basename($post['image']));
        
        // 2. Generate both web and absolute paths
        $post['image_url'] = '/assets/images/' . $filename;
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/' . basename($post['image']);
        
        // 3. Debug logging (temporary)
        error_log("Generated URL: {$post['image_url']}");
        error_log("Server path: {$absolute_path}");
        error_log("File exists: " . (file_exists($absolute_path) ? 'Yes' : 'No'));
        
        // 4. Fallback if file doesn't exist
        if (!file_exists($absolute_path)) {
            $post['image_url'] = '/assets/images/default.jpg';
            error_log("Missing image: {$post['image']}");
        }
    } else {
        $post['image_url'] = '/assets/images/default.jpg';
    }
}

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);
?>