<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// Start secure session
session_start();

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db.php';

// Verify user is logged in
if (empty($_SESSION['user_id'])) {
    header("Location: /../user/login.php");
    exit();
}

// Initialize HTML Purifier
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 'p,br,a[href],strong,em,ul,ol,li,img[src|alt],h1,h2,h3,h4');
$purifier = new HTMLPurifier($config);

if (isset($_POST['create'])) {
    try {
        // Validate and sanitize inputs
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
        $fname = htmlspecialchars(trim($_POST['fname'] ?? ''));
        $summary = $purifier->purify(trim($_POST['summary'] ?? ''));
        $content = $purifier->purify(trim($_POST['content'] ?? ''));
        $category = htmlspecialchars(trim($_POST['category'] ?? ''));
        $created_at = date('Y-m-d H:i:s');

        // Handle image upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png'];
            $detected_type = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($detected_type, $allowed_types)) {
                $upload_dir = __DIR__ . '/../../public/assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '', $_FILES['image']['name']);
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = $filename;
                }
            }
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO posts 
            (user_id, fname, title, summary, content, category, created_at, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("isssssss", 
            $_SESSION['user_id'],
            $fname,
            $title,
            $summary,
            $content,
            $category,
            $created_at,
            $image_path
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Post added successfully";
            header("Location: blog.php");
            exit();
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Post creation error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to create post. Please try again.";
        header("Location: blog.php");
        exit();
    }
}

if (isset($_POST['update'])) {
    try {
        $post_id = (int)$_POST['post_id'];
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
        $fname = htmlspecialchars(trim($_POST['fname'] ?? ''));
        $summary = $purifier->purify(trim($_POST['summary'] ?? ''));
        $content = $purifier->purify(trim($_POST['content'] ?? ''));
        $category = htmlspecialchars(trim($_POST['category'] ?? ''));

        // Handle image upload
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png'];
            $detected_type = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($detected_type, $allowed_types)) {
                $upload_dir = __DIR__ . '/../../public/assets/images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '', $_FILES['image']['name']);
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image = $filename;
                }
            }
        } else {
            // Keep existing image
            $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $image = $row['image'] ?? null;
        }

        // Update post
        $stmt = $conn->prepare("UPDATE posts SET 
            title = ?, fname = ?, summary = ?, content = ?, category = ?, image = ? 
            WHERE id = ? AND user_id = ?");
        
        $stmt->bind_param("ssssssii", 
            $title, $fname, $summary, $content, $category, $image, $post_id, $_SESSION['user_id']
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Post updated successfully!";
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Post update error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to update post.";
    }
    
    header("Location: blog.php");
    exit();
}