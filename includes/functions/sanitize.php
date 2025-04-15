<?php
function sanitizeOutput(string $data): string {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


function purifyHtml(string $dirtyHtml): string {
    require_once __DIR__ . '/../vendor/ezyang/HTMLPurifier.auto.php'; // Composer autoloader
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($dirtyHtml);
}

?>