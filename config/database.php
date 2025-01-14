<?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'chira');

    // เชื่อมต่อกับฐานข้อมูล
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // ตั้งค่า character set เป็น utf8mb4
    mysqli_set_charset($link, "utf8mb4");

    // ตรวจสอบการเชื่อมต่อ
    if ($link === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
?>
