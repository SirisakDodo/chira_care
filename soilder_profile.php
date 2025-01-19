<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: soilder_login.php"); // พาไปหน้า login ถ้ายังไม่ได้ล็อกอิน
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลผู้ใช้</title>
</head>
<body>
    <h1>ยินดีต้อนรับ <?php echo htmlspecialchars($user['soldier_id_card']); ?></h1>
    <p>ชื่อ: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
    <p>เลขบัตรประชาชน: <?php echo htmlspecialchars($user['soldier_id_card']); ?></p>
    <br><a href="form.php">ทำแบบประเมินการสูบบุหรี่</a><br/>
    
    <br><a href="logout.php">ออกจากระบบ</a><br/>
</body>
</html>
    