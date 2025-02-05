<?php
require_once __DIR__ . '/../config/database.php';

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ตั้งค่าการอ่านภาษาไทยให้ถูกต้อง
mysqli_set_charset($link, "utf8mb4");

// ดึงจำนวนทหารทั้งหมด
$total_soldiers_result = mysqli_query($link, "SELECT COUNT(*) AS total FROM soldier");
$total_soldiers = $total_soldiers_result ? mysqli_fetch_assoc($total_soldiers_result)['total'] : 0;

// ดึงจำนวนรอบการฝึกทั้งหมด
$total_rotations_result = mysqli_query($link, "SELECT COUNT(*) AS total FROM rotation");
$total_rotations = $total_rotations_result ? mysqli_fetch_assoc($total_rotations_result)['total'] : 0;

// ดึงจำนวนรอบฝึกซ้อมทั้งหมด
$total_trainings_result = mysqli_query($link, "SELECT COUNT(*) AS total FROM training");
$total_trainings = $total_trainings_result ? mysqli_fetch_assoc($total_trainings_result)['total'] : 0;

// Query ดึงข้อมูลทหาร
$query = "
    SELECT
        s.soldier_id,
        s.soldier_id_card,
        s.first_name,
        s.last_name,
        COALESCE(r.rotation, 'N/A') AS rotation_name,
        COALESCE(t.training_unit, 'N/A') AS training_unit_name,
        COALESCE(s.affiliated_unit, 'N/A') AS affiliated_unit,
        COALESCE(s.weight_kg, 'N/A') AS weight_kg,
        COALESCE(s.height_cm, 'N/A') AS height_cm,
        COALESCE(s.medical_allergy_food_history, 'ไม่มีข้อมูล') AS allergy,
        COALESCE(s.underlying_diseases, 'ไม่มีข้อมูล') AS diseases,
        COALESCE(s.selection_method, 'ไม่ระบุ') AS selection_method,
        COALESCE(s.service_duration, 'N/A') AS service_duration,
        s.soldier_image
    FROM soldier s
    LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
    LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
    ORDER BY s.soldier_id DESC
";


$result = mysqli_query($link, $query);

// ตรวจสอบ Query ว่าทำงานได้หรือไม่
if (!$result) {
    die("Query failed: " . mysqli_error($link)); // แสดง SQL Error ถ้า Query ล้มเหลว
}
?>