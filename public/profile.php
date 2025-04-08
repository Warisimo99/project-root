<?php
session_start();

$resolved_path = __DIR__ . '/../db.php';
require_once '$resolved_path';

// Fetch the user's posts
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

// Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user data
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("User not found.");
    }

    // Fetch blog statistics
    $sql_posts = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?";
    $stmt_posts = $conn->prepare($sql_posts);
    if (!$stmt_posts) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_posts->bind_param("i", $user_id);
    $stmt_posts->execute();
    $post_stats = $stmt_posts->get_result()->fetch_assoc();

    // Fetch likes and comments
    $sql_likes = "SELECT COUNT(*) as like_count FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)";
    $stmt_likes = $conn->prepare($sql_likes);
    if (!$stmt_likes) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_likes->bind_param("i", $user_id);
    $stmt_likes->execute();
    $like_stats = $stmt_likes->get_result()->fetch_assoc();

    $sql_comments = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)";
    $stmt_comments = $conn->prepare($sql_comments);
    if (!$stmt_comments) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_comments->bind_param("i", $user_id);
    $stmt_comments->execute();
    $comment_stats = $stmt_comments->get_result()->fetch_assoc();

    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update personal information
        if (isset($_POST['update_info'])) {
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $email = trim($_POST['email']);

            $update_sql = "UPDATE users SET fname = ?, lname = ?, email = ?, WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $update_stmt->bind_param("ssssi", $fname, $lname, $email, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['success'] = "Profile updated successfully.";
                header("Location: profile.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to update profile.";
                header("Location: profile.php");
                exit();
            }
        }

        // Change password
        if (isset($_POST['change_password'])) {
            $current_password = trim($_POST['current_password']);
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            // Verify passwords match
            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "New passwords do not match.";
                header("Location: profile.php");
                exit();
            }

            // Verify current password
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!password_verify($current_password, $result['password'])) {
                $_SESSION['error'] = "Current password is incorrect.";
                header("Location: profile.php");
                exit();
            }

            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['success'] = "Password updated successfully.";
                header("Location: profile.php");
                exit();
            } else {
                $_SESSION['error'] = "Failed to update password.";
                header("Location: profile.php");
                exit();
            }
        }

        // Delete account
        if (isset($_POST['delete_account'])) {
            $confirm_password = trim($_POST['confirm_delete']);

            // Verify password
            $sql = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (password_verify($confirm_password, $result['password'])) {
                // Delete user's posts, comments, likes, etc.
                $conn->begin_transaction();

                try {
                    // Delete likes on user's posts
                    $conn->query("DELETE FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = $user_id)");

                    // Delete comments on user's posts
                    $conn->query("DELETE FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = $user_id)");

                    // Delete user's posts
                    $conn->query("DELETE FROM posts WHERE user_id = $user_id)");

                    // Delete user's account
                    $conn->query("DELETE FROM users WHERE id = $user_id)");

                    $conn->commit();

                    // Clear session and redirect to home
                    session_destroy();
                    header("Location: index.php?message=account_deleted");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $_SESSION['error'] = "Failed to delete account. Please try again.";
                    header("Location: profile.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Incorrect password. Account deletion cancelled.";
                header("Location: profile.php");
                exit();
            }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/dashstyle.css">
    <title>Profile - DashView</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Profile </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                </ul>
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

        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user['fname']); ?></h2>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Profile Information</h4>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            <div class="card-body">
                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Personal Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">Blog Statistics</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab" aria-controls="delete" aria-selected="false">Delete Account</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="profileTabsContent">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <form action="profile.php" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <button type="submit" name="update_info" class="btn btn-primary">Update Information</button>
                        </form>
                    </div>
                    
                    <!-- Password Change Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="profile.php" method="post">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                    
                    <!-- Blog Statistics Tab -->
                    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Published Posts</h5>
                                        <p class="display-4"><?php echo $post_stats['post_count']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Likes</h5>
                                        <p class="display-4"><?php echo $like_stats['like_count']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Comments</h5>
                                        <p class="display-4"><?php echo $comment_stats['comment_count']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="blog.php" class="btn btn-outline-primary">View All Posts</a>
                    </div>
                    
                    <!-- Delete Account Tab -->
                    <div class="tab-pane fade" id="delete" role="tabpanel" aria-labelledby="delete-tab">
                        <div class="alert alert-danger">
                            <h5>Warning: This action cannot be undone!</h5>
                            <p>Deleting your account will permanently remove all your data, including posts, comments, and likes.</p>
                        </div>
                        <form action="profile.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                            <div class="mb-3">
                                <label for="confirm_delete" class="form-label">Type your password to confirm</label>
                                <input type="password" class="form-control" id="confirm_delete" name="confirm_delete" required>
                            </div>
                            <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account</button>
                        </form>
                    </div>
                </div>
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
        import { initializeSpinner } from '/assets/js/spinner.js';
        initializeSpinner();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
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