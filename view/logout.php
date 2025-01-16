<?php
// เริ่มต้นเซสชัน
session_start();

// ลบข้อมูลทั้งหมดในเซสชัน
session_unset();

// ทำลายเซสชัน
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้า login
header("Location: login.php");
exit();
?>
