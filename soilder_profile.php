<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: soilder_login.php"); // Redirect to login if not logged in
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            color: #333;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
            color: #555;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #0056b3;
        }

        .logout-link {
            background-color: #dc3545;
        }

        .logout-link:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>ยินดีต้อนรับ <?php echo htmlspecialchars($user['soldier_id_card']); ?></h1>
        <p>ชื่อ: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        <p>เลขบัตรประชาชน: <?php echo htmlspecialchars($user['soldier_id_card']); ?></p>
        <a href="form.php">ทำแบบประเมินการสูบบุหรี่</a>
        <a href="logout.php" class="logout-link">ออกจากระบบ</a>
    </div>

</body>

</html>