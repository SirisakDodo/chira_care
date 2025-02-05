<?php
require_once __DIR__ . '/../config/database.php';
if (!isset($link)) {
    die("Database connection failed!");
}


// ตรวจสอบว่าได้อัปโหลดไฟล์ CSV หรือไม่
if (isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, "r");

    if ($handle !== false) {
        fgetcsv($handle);

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            // ตรวจสอบค่าจาก CSV
            $soldier_id_card = isset($row[0]) ? mysqli_real_escape_string($link, $row[0]) : '';
            $first_name = isset($row[1]) ? mysqli_real_escape_string($link, $row[1]) : '';
            $last_name = isset($row[2]) ? mysqli_real_escape_string($link, $row[2]) : '';
            $rotation_id = isset($row[3]) ? intval($row[3]) : 0;
            $training_unit_id = isset($row[4]) ? intval($row[4]) : 0;
            $affiliated_unit = isset($row[5]) ? mysqli_real_escape_string($link, $row[5]) : NULL;
            $weight_kg = isset($row[6]) ? floatval($row[6]) : 0;
            $height_cm = isset($row[7]) ? intval($row[7]) : 0;
            $medical_allergy_food_history = isset($row[8]) ? mysqli_real_escape_string($link, $row[8]) : NULL;
            $underlying_diseases = isset($row[9]) ? mysqli_real_escape_string($link, $row[9]) : NULL;
            $selection_method = isset($row[10]) ? mysqli_real_escape_string($link, $row[10]) : '';
            $service_duration = isset($row[11]) ? intval($row[11]) : 0;


            if (!empty($soldier_id_card) && !empty($first_name) && !empty($last_name)) {

                $sql = "INSERT INTO Soldier (soldier_id_card, first_name, last_name, rotation_id, training_unit_id, affiliated_unit, weight_kg, height_cm, medical_allergy_food_history, underlying_diseases, selection_method, service_duration)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($link, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssiissssssi", $soldier_id_card, $first_name, $last_name, $rotation_id, $training_unit_id, $affiliated_unit, $weight_kg, $height_cm, $medical_allergy_food_history, $underlying_diseases, $selection_method, $service_duration);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "ข้อมูลทหารที่เพิ่มสำเร็จ: $soldier_id_card<br>";
                    } else {
                        echo "❌ การเพิ่มข้อมูลทหารไม่สำเร็จสำหรับ: $soldier_id_card <br>";
                        echo "ข้อผิดพลาด: " . mysqli_stmt_error($stmt); // แสดงข้อผิดพลาดจากการดำเนินการ
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "❌ ไม่สามารถเตรียมคำสั่ง SQL ได้: " . mysqli_error($link); // แสดงข้อผิดพลาดหากไม่สามารถเตรียมคำสั่ง SQL
                }
            }
        }
        fclose($handle);
        echo "📌 ข้อมูลจากไฟล์ CSV ถูกบันทึกเรียบร้อยแล้ว!";
    } else {
        echo "❌ ไม่สามารถเปิดไฟล์ CSV ได้";
    }
} else {
    echo "❌ กรุณาอัปโหลดไฟล์ CSV ก่อนดำเนินการ";
}

?>