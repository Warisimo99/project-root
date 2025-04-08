<?php
// Must be the VERY FIRST line with no whitespace before
session_start();
ob_start(); // Start output buffering

$resolved_path = __DIR__ . '/../db.php';
error_log("Resolved path: ".$resolved_path);
error_log("File exists:".(file_exists($resolved_path) ? 'Yes' : 'No'));
require_once $resolved_path;

// Check connection
if (!$conn) {
    ob_end_clean(); // Clean buffer before headers
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch posts
$sql = "SELECT id, title, image, content, created_at FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    ob_end_clean();
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]));
}

$posts = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

foreach ($posts as &$post) {
    if (!empty($post['image'])) {
        // Properly encode filenames (especially important for spaces/special chars)
        $filename = rawurlencode(basename($post['image']));
        $post['image_url'] = '/assets/images/' . $filename;
        
        // Debugging path (optional)
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/' . basename($post['image']);
        error_log("Image: {$filename} | Exists: " . (file_exists($absolutePath) ? 'Yes' : 'No'));
    } else {
        $post['image_url'] = ''; // Consider setting a default image URL here
        error_log("Empty image for post ID: " . ($post['id'] ?? 'unknown'));
    }
    
    // Add other post processing if needed
    $post['formatted_date'] = date('F j, Y', strtotime($post['created_at']));
    $post['excerpt'] = substr($post['content'], 0, 150) . (strlen($post['content']) > 150 ? '...' : '');
}

// Clean buffer and send JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($posts);
exit(); // Important to prevent any additional output
?>