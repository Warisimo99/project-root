<?php
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token']) || 
        (isset($_SESSION['csrf_token_expiry']) && time() > $_SESSION['csrf_token_expiry'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiry'] = time() + 900;
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    if (empty($token) || 
        empty($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    
    if (empty($_SESSION['csrf_token_expiry']) || 
        time() > $_SESSION['csrf_token_expiry']) {
        return false;
    }
    
    // Generate new token for next request
    generateCsrfToken();
    return true;
}
?>