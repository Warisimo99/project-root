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
        // Ensure proper URL encoding for filenames
        $filename = rawurlencode(basename($post['image']));
        
        // Use absolute web path (no /public in URL)
        $post['image_url'] = '/assets/images/' . $filename;
        
        // Debug logging
        error_log("Generated image URL: " . $post['image_url']);
        
        // Fallback if image doesn't exist
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/' . basename($post['image']);
        if (!file_exists($absolute_path)) {
            $post['image_url'] = '/assets/images/default.jpg';
            error_log("Image missing: " . $post['image']);
        }
    } else {
        $post['image_url'] = '/assets/images/default.jpg';
    }
    
    // Format other post data
    $post['formatted_date'] = date('F j, Y', strtotime($post['created_at']));
    $post['excerpt'] = substr($post['content'], 0, 150) . (strlen($post['content']) > 150 ? '...' : '');
}

// Return the posts as JSON
header('Content-Type: application/json');
echo json_encode($posts);
?>