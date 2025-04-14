<?php
// MUST BE AT THE VERY TOP (before any HTML/output)
session_start();

// Nuclear option for session destruction
$_SESSION = []; // Empty session data
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Kill page caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");

// Redirect
header("Location: register.php");
exit();
?>