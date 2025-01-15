<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $soldierId = $_POST['id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $rotationId = $_POST['rotation_id'];
    $trainingUnitId = $_POST['training_unit_id'];
    $affiliatedUnit = $_POST['affiliated_unit'];  // รับค่าจากฟอร์ม
    $weightKg = $_POST['weight_kg'];
    $heightCm = $_POST['height_cm'];
    $medicalAllergyFoodHistory = $_POST['medical_allergy_food_history'];
    $underlyingDiseases = $_POST['underlying_diseases'];
    $selectionMethod = $_POST['selection_method'];
    $serviceDuration = $_POST['service_duration'];

    // รับข้อมูลภาพที่อัปโหลด
    $soldierImage = null;
    if (isset($_FILES['soldier_image']) && $_FILES['soldier_image']['error'] == 0) {
        $soldierImage = file_get_contents($_FILES['soldier_image']['tmp_name']);
    }

    // สร้างคำสั่ง SQL เพื่ออัปเดตข้อมูลทหาร
    $sqlUpdate = "UPDATE Soldier SET
                    first_name = ?,
                    last_name = ?,
                    rotation_id = ?,
                    training_unit_id = ?,
                    affiliated_unit = ?,
                    weight_kg = ?,
                    height_cm = ?,
                    medical_allergy_food_history = ?,
                    underlying_diseases = ?,
                    selection_method = ?,
                    service_duration = ?,
                    soldier_image = ?
                  WHERE soldier_id_card = ?";

    // เตรียมคำสั่ง SQL
    $stmt = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param($stmt, "ssiiissssssss",
        $firstName,
        $lastName,
        $rotationId,
        $trainingUnitId,
        $affiliatedUnit,  // ใช้ค่าของ affiliated_unit ที่รับมา
        $weightKg,
        $heightCm,
        $medicalAllergyFoodHistory,
        $underlyingDiseases,
        $selectionMethod,
        $serviceDuration,
        $soldierImage,  // อัปเดตภาพ
        $soldierId);  // ใช้ soldier_id_card ที่ส่งมาในฟอร์ม

    // ดำเนินการคำสั่ง SQL
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('ข้อมูลทหารถูกอัปเดตสำเร็จ'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.location.href = 'edit_soldier.php?id=$soldierId';</script>";
    }

    mysqli_stmt_close($stmt);
}
?>
