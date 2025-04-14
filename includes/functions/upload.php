<?php
function validateImageUpload(array $file): string|false {
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedMimeTypes)) {
        return false;
    }
    $filename = uniqid() . '_' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], __DIR__ . '/../../uploads/' . $filename);
    return $filename;
}
?>