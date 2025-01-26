<?php
session_start();
session_unset(); // ล้างข้อมูลใน session
session_destroy(); // ทำลาย session
header("Location: soilder_login.php"); // รีไดเร็กต์ไปยังหน้าล็อกอิน
exit();
?>