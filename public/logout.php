<?php
session_start();

// Generate new CSRF token BEFORE destroying session
$newCsrfToken = bin2hex(random_bytes(32));

// Clear session data
$_SESSION = [];

// Destroy session completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}
session_destroy();


session_start();
$_SESSION['csrf_token'] = $newCsrfToken;
$_SESSION['csrf_token_expiry'] = time() + 900;


header("Location: register.php");
exit();
?>