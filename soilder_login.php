<?php
session_start();
require_once 'config.php'; // ใช้ $link ที่กำหนดใน config.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_card = $_POST['id_card'];

    // ตรวจสอบข้อมูลในฐานข้อมูล
    $stmt = $link->prepare("SELECT * FROM soldier WHERE soldier_id_card = ?");
    $stmt->bind_param("s", $id_card);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user; // เก็บข้อมูลใน session
        header("Location: soilder_profile.php"); // พาไปหน้าโปรไฟล์
        exit();
    } else {
        echo "<script>alert('เลขบัตรประชาชนไม่ถูกต้อง!');</script>";
    }

    $stmt->close();
}
$link->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* (ใส่ CSS ตามโค้ดก่อนหน้า) */
    </style>
</head>
<body>
    <div class="login-container">
        <h1>เข้าสู่ระบบ</h1>
        <form method="POST" action="soilder_login.php">
            <label for="id-card">เลขบัตรประชาชน</label>
            <input type="text" id="id-card" name="id_card" maxlength="13" placeholder="กรอกเลขบัตรประชาชน" required>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
