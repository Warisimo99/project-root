<?php
spl_autoload_register(function($class) {
    require dirname(__DIR__) . "/includes/" . str_replace('\\', '/', $class) . '.php';
});

require dirname(__DIR__) . '/includes/db.php';
?>