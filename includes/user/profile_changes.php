<?php
include('/db.php');

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
        $user_name = trim($_POST['user_name']);

        $update_sql = "UPDATE users SET user_name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $update_stmt->bind_param("ssssi", $user_name, $user_id);

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