<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category']; // หมวดหมู่ที่เลือก เช่น suicide, depression, alcohol, drug
    $question_text = $_POST['question_text'];

    // ตรวจสอบหมวดหมู่และเลือกตารางที่เหมาะสม
    $table_map = [
        'suicide' => 'suicide_assessment',
        'depression' => 'depression_assessment',
        'alcohol' => 'alcohol_assessment',
        'drug' => 'drug_assessment'
    ];

    if (array_key_exists($category, $table_map)) {
        $table = $table_map[$category];

        // เพิ่มคำถามเข้าไปในฐานข้อมูล
        $sql = "INSERT INTO $table (question_text) VALUES (?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('s', $question_text);

        if ($stmt->execute()) {
            echo "เพิ่มคำถามสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาด: " . $stmt->error;
        }
    } else {
        echo "หมวดหมู่ไม่ถูกต้อง!";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มคำถาม</title>
</head>
<body>
    <h3>เพิ่มคำถามใหม่</h3>
    <form method="post" action="">
        <label for="category">หมวดหมู่:</label>
        <select name="category" id="category" required>
            <option value="suicide">การฆ่าตัวตาย</option>
            <option value="depression">ซึมเศร้า</option>
            <option value="alcohol">ติดแอลกอฮอล์</option>
            <option value="drug">ติดยา</option>
        </select>
        <br><br>
        <label for="question_text">คำถาม:</label>
        <textarea name="question_text" id="question_text" rows="4" required></textarea>
        <br><br>
        <button type="submit">เพิ่มคำถาม</button>
    </form>
</body>
</html>
