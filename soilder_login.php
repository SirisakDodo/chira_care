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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .alert {
            color: #e74c3c;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
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