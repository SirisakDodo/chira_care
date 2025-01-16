<?php
// เริ่มต้น session
session_start();

// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once '../config/database.php';

// กำหนดข้อความผิดพลาด
$error = '';

// ตรวจสอบว่าแบบฟอร์มถูกส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $training_unit = trim($_POST['training_unit']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าผู้ใช้กรอกข้อมูลหรือไม่
    if (empty($training_unit) || empty($password)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    } else {
        // สร้างคำสั่ง SQL สำหรับตรวจสอบข้อมูล
        $sql = "SELECT mu.*, t.training_unit
                FROM militaryusers mu
                JOIN training t ON mu.training_unit_id = t.training_unit_id
                WHERE t.training_unit = ?";

        // เตรียมคำสั่ง SQL
        $stmt = $link->prepare($sql);

        // ผูกค่ากับคำสั่ง SQL
        $stmt->bind_param("s", $training_unit); // 's' หมายถึงตัวแปรแบบ string

        // เรียกใช้คำสั่ง SQL
        $stmt->execute();

        // รับผลลัพธ์จากคำสั่ง SQL
        $result = $stmt->get_result();

        // ตรวจสอบว่าพบผู้ใช้หรือไม่
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ตรวจสอบรหัสผ่านโดยใช้ password_verify
            if (password_verify($password, $user['password'])) {
                // ถ้ารหัสผ่านถูกต้อง ให้สร้าง session และเปลี่ยนเส้นทางไปที่หน้า dashboard
                $_SESSION['user'] = array(
                    'user_id' => $user['user_id'],
                    'training_unit_id' => $user['training_unit_id'],
                    'training_unit' => $user['training_unit']
                );
                header("Location: insert_medicalreport.php"); // เปลี่ยนเส้นทางไปที่หน้า dashboard
                exit();
            } else {
                // ถ้ารหัสผ่านไม่ถูกต้อง
                $error = "รหัสผ่านไม่ถูกต้อง!";
            }
        } else {
            // ถ้าไม่พบผู้ใช้
            $error = "ไม่พบข้อมูลผู้ใช้!";
        }

        // ปิดการเชื่อมต่อคำสั่ง SQL
        $stmt->close();
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
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
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 300px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <?php if (!empty($error)) { echo '<div class="error">' . $error . '</div>'; } ?>
            <label for="training_unit">Training Unit:</label>
            <input type="text" id="training_unit" name="training_unit" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>
