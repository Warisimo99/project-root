<?php
function get_db_connection() {
    $db_server = "if0ck476y7axojpg.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
    $db_user = "qcy2v5juybnpxza9";
    $db_pass = "qwv9cxtw261dei1i";
    $db_name = "l46vmk4fu2x5xz5t";

    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("Connection to database '$db_name' failed: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}



$conn = get_db_connection();
?>