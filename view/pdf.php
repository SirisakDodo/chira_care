<?php
require __DIR__ . '/../vendor/autoload.php';  // ตรวจสอบเส้นทางให้ถูกต้อง
require_once '../config/database.php';  // เชื่อมต่อกับฐานข้อมูล

// สร้างไฟล์ PDF ด้วย mPDF
$mpdf = new \Mpdf\Mpdf([
    'fontDir' => [__DIR__ . '/../vendor/mpdf/mpdf/ttfonts'],  // ระบุที่อยู่ของฟอนต์
    'fontdata' => [
        'sarabun' => [
            'R' => 'Sarabun-Regular.ttf',      // ฟอนต์ปกติ
            'B' => 'Sarabun-Bold.ttf',         // ฟอนต์ตัวหนา
            'I' => 'Sarabun-Italic.ttf',       // ฟอนต์ตัวเอียง
            'BI' => 'Sarabun-BoldItalic.ttf',  // ฟอนต์ตัวหนาและเอียง
        ]
    ]
]);

$mpdf->SetFont('sarabun', 'I');  // ใช้ฟอนต์ Sarabun ตัวเอียง

// ดึงข้อมูลจากฐานข้อมูล
$query = "SELECT * FROM Soldier";  // แก้ไขให้ตรงกับชื่อของตาราง
$result = mysqli_query($link, $query);  // ดำเนินการ SQL

// ตรวจสอบว่ามีข้อมูลหรือไม่
if (!$result) {
    die("ERROR: ไม่สามารถดึงข้อมูลจากฐานข้อมูล: " . mysqli_error($link));
}

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

// ดึงข้อมูลและเพิ่มลงใน HTML
while ($soldier = mysqli_fetch_assoc($result)) {
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

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($link);
?>
