<?php
function validateEmail(string $email): string|false {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword(string $password): bool {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
}

function validateText(string $text, int $maxLength = 255): string|false {
    $text = trim($text);
    return (strlen($text) <= $maxLength) ? $text : false;
}

function validateAlphanumeric(string $input, int $maxLength = 100): string|false {
    return preg_match('/^[a-zA-Z0-9\s\-_.,!?]+$/', $input) ? $input : false;
}
?>