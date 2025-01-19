<?php
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงคำถามทั้งหมดจากฐานข้อมูลเพื่อวน loop
    $sql = "SELECT question_id FROM smoking_assessment";
    $result = $link->query($sql);  // ใช้ $link แทน $conn

    $totalScore = 0; // เก็บคะแนนรวม
    $soldier_id = 1; // รหัสประจำตัวทหาร (สมมติ)

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            $answer_value = isset($_POST["answer_$question_id"]) ? intval($_POST["answer_$question_id"]) : 0;

            // เพิ่มคะแนนลงคะแนนรวม
            $totalScore += $answer_value;

            // บันทึกคำตอบลงฐานข้อมูล
            $insertSql = "INSERT INTO answers (soldier_id, question_id, answer_value, submitted_at) 
                          VALUES (?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE answer_value = VALUES(answer_value), submitted_at = VALUES(submitted_at)";
            $stmt = $link->prepare($insertSql);  // ใช้ $link แทน $conn
            $stmt->bind_param("iii", $soldier_id, $question_id, $answer_value);
            $stmt->execute();
        }
    }

    // แสดงผลลัพธ์
    echo "<h1>ผลรวมคะแนน: $totalScore</h1>";
    if ($totalScore < 20) {
        echo "คุณมีอาการเสพติดการสูบบุหรี่ในเบื้องต้น";
    } elseif ($totalScore >= 20 && $totalScore <= 30) {
        echo "คุณมีอาการเสพติดการสูบบุหรี่ในขั้นปานกลาง";
    } else {
        echo "คุณมีอาการเสพติดการสูบบุหรี่ในขั้นรุนแรง";
    }

     // เพิ่มปุ่มเพื่อกลับไปยังหน้า soilder_profile.php
     echo "<br><a href='soilder_profile.php'><button>กลับไปยังโปรไฟล์</button></a>";

    // แสดงประวัติการทำแบบฟอร์ม
    echo "<h2>ประวัติการทำแบบฟอร์ม</h2>";

    // ดึงประวัติการทำแบบฟอร์มจากฐานข้อมูล
    $historySql = "SELECT a.question_id, a.answer_value, sa.question_text, a.submitted_at
                   FROM answers a
                   JOIN smoking_assessment sa ON a.question_id = sa.question_id
                   WHERE a.soldier_id = $soldier_id
                   ORDER BY a.submitted_at DESC";

    $historyResult = $link->query($historySql);  // ใช้ $link แทน $conn

    if ($historyResult->num_rows > 0) {
        echo "<table border='1'><tr><th>คำถาม</th><th>คำตอบ</th><th>วันที่ทำแบบฟอร์ม</th></tr>";
        while ($historyRow = $historyResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $historyRow['question_text'] . "</td>";
            echo "<td>" . $historyRow['answer_value'] . "</td>";
            echo "<td>" . $historyRow['submitted_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "ไม่มีประวัติการทำแบบฟอร์ม";
    }

}
?>
