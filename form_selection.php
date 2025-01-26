<?php
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: soilder_login.php");
    exit();
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกทำแบบฟอร์ม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">เลือกทำแบบฟอร์ม</h1>
        <div class="card mt-4">
            <div class="card-body">
                <h4>โปรดเลือกแบบฟอร์มที่ต้องการทำ:</h4>
                <div class="list-group mt-3">
                    <a href="smoking_form.php" class="list-group-item list-group-item-action">
                        แบบฟอร์มบุหรี่
                    </a>
                    <a href="alcohol_form.php" class="list-group-item list-group-item-action">
                        แบบฟอร์มแอลกอฮอล์
                    </a>
                    <a href="drug_form.php" class="list-group-item list-group-item-action">
                        แบบฟอร์มติดยา
                    </a>
                    <a href="depression_form.php" class="list-group-item list-group-item-action">
                        แบบฟอร์มซึมเศร้า
                    </a>
                    <a href="suicide_form.php" class="list-group-item list-group-item-action">
                        แบบฟอร์มฆ่าตัวตาย
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="soilder_profile.php" class="btn btn-secondary">กลับไปยังโปรไฟล์</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>