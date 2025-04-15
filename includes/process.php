<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../functions/sanitize.php';
require_once __DIR__ . '/../functions/upload.php';
require_once __DIR__ . '/../functions/validation.php';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

// Initialize HTML Purifier
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 'p,br,a[href],strong,em,ul,ol,li,img[src|alt],h1,h2,h3,h4');
$config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
$purifier = new HTMLPurifier($config);

$user_id = $_SESSION['user_id'];

    if (isset($_POST['create'])) {
        include("db.php");

        // Retrieve and sanitize form data
        $title = htmlspecialchars(trim($_POST['title']));
        $fname = htmlspecialchars(trim($_POST['fname']));
        $summary = htmlspecialchars(trim($_POST['summary']));
        $content = htmlspecialchars(trim($_POST['content']));
        $category = htmlspecialchars(trim($_POST['category']));
        $created_at = trim($_POST['created_at']);

        // Handle image upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                $uploadDir = __DIR__ . '/public/assets/uploads/';
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                $imagePath = $filename;
            }
        }

        // Insert data into the database using prepared statements
        $sqlInsert = "INSERT INTO posts (user_id, fname, title, summary, content, category, created_at, image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = mysqli_prepare($conn, $sqlInsert);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $fname, $title, $summary, $content, $category, $created_at, $image_path);

            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["create"] = "Post added successfully";
                header("Location: /public/blog.php");
                exit();
            } else {
                die("Error: " . mysqli_error($conn));
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            die("Error preparing statement: " . mysqli_error($conn));
        }
    }
    if (isset($_POST['update'])) {
        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];
        $title = $_POST['title'];
        $fname = $_POST['fname'];
        $summary = $_POST['summary'];
        $content = $_POST['content'];
        $category = $_POST['category'];

        // Handle image upload (if a new image is provided)
        if ($_FILES['image']['name']) {
            $image = $_FILES['image']['name'];
            $target_dir = "/public/assets/images";
            $target_file = $target_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        } else {
            // Keep the existing image if no new image is uploaded
            $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $image = $row['image'];
        }

        // Update the post in the database
        $stmt = $conn->prepare("UPDATE posts SET title = ?, fname = ?, summary = ?, content = ?, category = ?, image = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssssii", $title, $fname, $summary, $content, $category, $image, $post_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Post updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update post.";
        }
    }
?>