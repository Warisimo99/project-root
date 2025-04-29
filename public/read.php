<?php
// Start the session
session_start();

require_once __DIR__ . '/../includes/db.php';


// Check if the post ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: error.php?message=Post ID is missing.");
    exit();
}


$postId = intval($_GET['id']);

// Fetch the post from the database
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $postId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: error.php?message=Post not found.");
    exit();
}

$post = mysqli_fetch_assoc($result);

// Fetch the number of likes for the post
$sql = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $postId);
mysqli_stmt_execute($stmt);
$likesResult = mysqli_stmt_get_result($stmt);
$likeCount = mysqli_fetch_assoc($likesResult)['like_count'];

// Fetch comments for the post with user details
$sql = "SELECT comments.*, users.fname 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $postId);
mysqli_stmt_execute($stmt);
$commentsResult = mysqli_stmt_get_result($stmt);
$comments = mysqli_fetch_all($commentsResult, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c8d66c9de4.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/dashstyle.css">
    <title>Read More</title>
</head>
<body>
    <div class="container mt-5">

      <!-- Display success/error messages -->
      <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <section class="d-flex">
            <main class="flex-grow-1">
                <!-- Post Title -->
                <h1 class="text-center mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>

               <!-- Post Image -->
               <img src="public/assets/images/<?php echo htmlspecialchars(basename($post['image'])); ?>">

                <!-- Post Details -->
                <div class="card">
                    <div class="card-body">
                        <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($post['fname']); ?></p>
                        <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($post['category']); ?></p>
                        <p class="card-text"><small class="text-muted">Posted on: <?php echo htmlspecialchars($post['created_at']); ?></small></p>
                        <hr>
                        <!-- Full Post Content -->
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <form action="/../includes/post_operations/add_like.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="btn btn-success">Like (<?php echo $likeCount; ?>)</button>
                    </form>
                </div>
                 <!-- Comments Section -->
                 <div class="mt-5">
                    <h3>Comments</h3>
                    <?php if (empty($comments)): ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($comment['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                    <small class="text-muted"><?php echo $comment['created_at']; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="mt-5">
                    <h3>Leave a Comment</h3>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="/../includes/post_operations/add_comment.php" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    <?php else: ?>
                        <p>Please <a href="register.php">log in</a> to leave a comment.</p>
                    <?php endif; ?>
                </div>

                <!-- Back to Blog Link -->
                <div class="mt-4">
                    <a href="blog.php" class="btn btn-secondary">Back to Blog</a>
                </div>
            </main>
        </section>
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
        import { initializeSpinner } from '/../public/assets/js/spinner.js';
        initializeSpinner();
    </script>
</body>
</html>