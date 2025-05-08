<?php
session_start();

// Generate CSRF token if it doesn't exist or has expired
if (empty($_SESSION['csrf_token']) || 
    (isset($_SESSION['csrf_token_expiry']) && time() > $_SESSION['csrf_token_expiry'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 900; // 15 minutes
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/user/csrf.php';



function getPostCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['post_count'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c8d66c9de4.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="stylesheet" href="assets/css/dashstyle.css">
    <title>Post</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Create Posts</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="blog.php">Blog</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
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
        
    <div style="display: flex; justify-content: center; padding: 20px;">
        <h2 class="btn btn-outline-light centered-heading">Manage Posts</h2>
    </div>
    <div class="d-flex flex-column justify-content-center align-items-center vh-100">
        <!-- Post Count Card -->
        <div class="card mb-4" style="width: 18rem;">
            <div class="card-body text-center">
                <h5 class="card-title">Your Posts</h5>
                <?php
                $post_count = getPostCount($conn, $_SESSION['user_id']);
                ?>
                <p class="card-text display-4"><?php echo $post_count; ?></p>
                <p class="card-text">Total posts uploaded.</p>
            </div>
        </div>

        <!-- Posts Dropdown -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Post
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createPostModal">Create New Post</a></li>
            </ul>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPostModalLabel">Create New Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/../includes/process.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <div class="form-field mb-4">
                            <input type="text" class="form-control" name="title" placeholder="Enter Title:" required>
                        </div>
                        <div class="form-field mb-4">
                            <input type="text" class="form-control" name="fname" placeholder="Enter Author Name:" required>
                        </div>
                        <div class="form-field mb-4">
                            <textarea name="summary" class="form-control" cols="30" rows="5" placeholder="Enter Summary:" required></textarea>
                        </div>
                        <div class="form-field mb-4">
                            <textarea name="content" class="form-control" cols="30" rows="10" placeholder="Enter Post:" required></textarea>
                        </div>
                        <div class="form-field mb-4">
                            <input type="text" class="form-control" name="category" placeholder="Enter Category:" required>
                        </div>
                        <div class="form-field mb-4">
                            <label for="image">Upload Image:</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        </div>
                        <input type="hidden" name="created_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <div class="form-field">
                            <input type="submit" class="btn btn-primary" value="Submit" name="create">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
    <h3>Your Posts</h3>
    <?php
    // Fetch posts from the database
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>
                    <p class="card-text"><strong>Author:</strong> ' . htmlspecialchars($row['fname']) . '</p>
                    <p class="card-text"><strong>Summary:</strong> ' . $row['summary'] . '</p> <!-- Already purified -->
                    <div class="card-text"><strong>Content:</strong> ' . $row['content'] . '</div> <!-- Already purified -->
                    <p class="card-text"><strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>
                    <p class="card-text"><strong>Created At:</strong> ' . htmlspecialchars($row['created_at']) . '</p>
                    
                    <div class="d-flex gap-2">
                        <a href="/../includes/edit_post.php?id=' . $row['id'] . '" class="btn btn-primary">Edit</a>
                        <a href="/../includes/post_operations/delete_post.php?id=' . $row['id'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>
                    </div>
                </div>
            </div>';
        }
       
    } else {
        echo '<p>No posts found.</p>';
    }
    ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>

    <script type="module" src ="/../public/assets/js/main.js"></script>
</body>

<footer class="footer bg-dark text-white py-4">
    <div class="container text-center">
        <p> Creator: Abdul . All rights reserved.</p>
        <p>
            <a href="Help.php" class="text-white">Help</a> |  
            <a href="https://www.tradingview.com/widget-docs/widgets/" class="text-white">Copyright &copy; 2025 TradingView, Inc.</a>
        </p>
    </div>
</footer>
</html>
