<?php
require_once '../config/database.php';  // รวมการเชื่อมต่อฐานข้อมูล

// ตรวจสอบการรับค่าจาก URL
if (isset($_GET['training_unit_id']) && is_numeric($_GET['training_unit_id'])) {
    $training_unit_id = intval($_GET['training_unit_id']);
    
    // สร้างคำสั่ง SQL ดึงข้อมูลทหารจากหน่วยฝึกที่ระบุ
    $soldiers_query = "SELECT soldier_id, CONCAT(first_name, ' ', last_name) AS full_name FROM soldier WHERE training_unit_id = ?";
    $stmt = mysqli_prepare($link, $soldiers_query);
    mysqli_stmt_bind_param($stmt, "i", $training_unit_id);
    mysqli_stmt_execute($stmt);
    $soldiers_result = mysqli_stmt_get_result($stmt);

    // แสดงตัวเลือกทหารใน <select>
    if (mysqli_num_rows($soldiers_result) > 0) {
        echo '<option value="">-- เลือกทหาร --</option>';
        while ($row = mysqli_fetch_assoc($soldiers_result)) {
            echo '<option value="' . $row['soldier_id'] . '">' . $row['full_name'] . '</option>';
        }
    } else {
        echo '<option value="">ไม่พบทหารในหน่วยนี้</option>';
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo '<option value="">ข้อมูลไม่ถูกต้อง</option>';
}
?>
