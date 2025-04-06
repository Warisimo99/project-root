<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /includes/user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

    if (isset($_POST['create'])) {
        include("db.php");

        // Retrieve and sanitize form data
        $title = trim($_POST['title']);
        $fname = trim($_POST['fname']);
        $summary = trim($_POST['summary']);
        $content = trim($_POST['content']);
        $category = trim($_POST['category']);
        $created_at = trim($_POST['created_at']);

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_name = basename($_FILES['image']['name']);
            $upload_dir = '/public/assets/images/'; 
            $image_path = $upload_dir . $image_name;

            // Ensure the upload directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
            }

            // Move the uploaded file
            if (!move_uploaded_file($image_tmp_name, $image_path)) {
                die("Failed to upload image.");
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