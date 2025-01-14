<?php
require __DIR__ . '/../vendor/autoload.php';  // ตรวจสอบเส้นทางให้ถูกต้อง
require_once '../config/database.php';  // เพิ่มการเชื่อมต่อกับฐานข้อมูล

// สร้างการเชื่อมต่อกับฐานข้อมูล
$pdo = new PDO('mysql:host=localhost;dbname=your_database_name', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ดึงข้อมูลจากฐานข้อมูล
$query = "SELECT * FROM soldiers";
$stmt = $pdo->query($query);
$soldiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// สร้างไฟล์ PDF ด้วย mPDF
$mpdf = new \Mpdf\Mpdf();

// สร้าง HTML สำหรับเนื้อหาของ PDF
$html = '<h1>รายชื่อทหาร</h1>';
$html .= '<table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>หมายเลขบัตรประชาชน</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>หน่วยฝึกอบรม</th>
                    <th>หน่วยที่เกี่ยวข้อง</th>
                    <th>น้ำหนัก (kg)</th>
                    <th>ส่วนสูง (cm)</th>
                    <th>ประวัติแพ้อาหาร</th>
                    <th>โรคประจำตัว</th>
                    <th>วิธีการคัดเลือก</th>
                    <th>ระยะเวลาการรับราชการ (ปี)</th>
                </tr>
            </thead>
            <tbody>';

foreach ($soldiers as $soldier) {
    $html .= '<tr>
                <td>' . htmlspecialchars($soldier['soldier_id_card']) . '</td>
                <td>' . htmlspecialchars($soldier['first_name']) . '</td>
                <td>' . htmlspecialchars($soldier['last_name']) . '</td>
                <td>' . htmlspecialchars($soldier['training_unit_id']) . '</td>
                <td>' . htmlspecialchars($soldier['affiliated_unit']) . '</td>
                <td>' . htmlspecialchars($soldier['weight_kg']) . '</td>
                <td>' . htmlspecialchars($soldier['height_cm']) . '</td>
                <td>' . htmlspecialchars($soldier['medical_allergy_food_history']) . '</td>
                <td>' . htmlspecialchars($soldier['underlying_diseases']) . '</td>
                <td>' . htmlspecialchars($soldier['selection_method']) . '</td>
                <td>' . htmlspecialchars($soldier['service_duration']) . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// เขียน HTML ลงใน PDF
$mpdf->WriteHTML($html);

// ส่งออกไฟล์ PDF
$mpdf->Output('soldiers_data.pdf', 'D');
?>
