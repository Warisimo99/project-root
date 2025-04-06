<?php
function get_db_connection() {
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "login";

    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("Connection to database '$db_name' failed: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}


$conn = get_db_connection();
?>