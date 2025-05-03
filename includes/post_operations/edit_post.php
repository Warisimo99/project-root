<?php
session_start();
require_once __DIR__ . '/../db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /../user/login.php");
    exit();
}

// Check if the post ID is provided
if (!isset($_GET['id'])) {
    header("Location: public/profile.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the post to edit
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
} else {
    echo "Post not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']); 

    
    if (empty($title) || empty($content) || empty($category)) {
        $_SESSION['error'] = "Title, content, and category cannot be empty.";
        header("Location: update_post.php?id=$post_id");
        exit();
    }

    // Update the post in the database
    $update_stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category = ? WHERE id = ? AND user_id = ?");
    if (!$update_stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $update_stmt->bind_param("sssii", $title, $content, $category, $post_id, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Post updated successfully.";
        header("Location: profile.php"); 
        exit();
    } else {
        $_SESSION['error'] = "Failed to update post.";
        header("Location: update_post.php?id=$post_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c8d66c9de4.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="http://aces-macbook-pro.local/project-root/assets/css/dashstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Edit Post</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Edit</h2>

        <!-- Display Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Edit Post Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Post</h5>
            </div>
            <div class="card-body">
                <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <textarea class="form-control" id="category" name="category" rows="10" required><?php echo htmlspecialchars($post['category']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="post.php?id=<?php echo $post_id; ?>" class="btn btn-secondary">Back to Post</a>
                </form>
            </div>
        </div>
    </div>
    <div id="spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Loading...</span>
            </button>
        </div>
    </div>

    <script type="module">
        import { initializeSpinner } from 'http://aces-macbook-pro.local/project-root/assets/js/spinner.js';
        initializeSpinner();
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>

</html>