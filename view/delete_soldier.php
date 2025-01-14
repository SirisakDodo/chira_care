<?php
// เชื่อมต่อฐานข้อมูล
require_once '../config/database.php';

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (isset($_POST['id'])) {
    $soldierId = $_POST['id'];

    // คำสั่ง SQL เพื่อลบข้อมูลทหาร
    $sql = "DELETE FROM Soldier WHERE soldier_id_card = '$soldierId'";

    // ดำเนินการคำสั่ง SQL
    if (mysqli_query($link, $sql)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($link);
?>
