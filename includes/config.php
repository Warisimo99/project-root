<?php 

// config.php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
define('HTTP_SERVER', 'https://shielded-garden-06013-bc1acbc5ebcd.herokuapp.com');
session_start(); // Start session here or later in individual files

?>