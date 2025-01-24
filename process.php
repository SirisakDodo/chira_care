<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT question_id FROM smoking_assessment";
    $result = $link->query($sql);

    $totalScore = 0;
    $soldier_id = 1;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            $answer_value = isset($_POST["answer_$question_id"]) ? intval($_POST["answer_$question_id"]) : 0;
            $totalScore += $answer_value;

            $insertSql = "INSERT INTO answers (soldier_id, question_id, answer_value, submitted_at)
                          VALUES (?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE answer_value = VALUES(answer_value), submitted_at = VALUES(submitted_at)";
            $stmt = $link->prepare($insertSql);
            $stmt->bind_param("iii", $soldier_id, $question_id, $answer_value);
            $stmt->execute();
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                color: #333;
            }

            .result-section,
            .history-section {
                margin-bottom: 30px;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .result-section h1 {
                color: #4CAF50;
            }

            .result-section p {
                font-size: 18px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th,
            td {
                padding: 10px;
                border: 1px solid #ddd;
            }

            th {
                background-color: #f2f2f2;
            }

            button {
                padding: 10px 20px;
                background-color: #2196F3;
                color: white;
                border: none;
                border-radius: 5px;
            }

            .back-link {
                margin-top: 15px;
                display: inline-block;
            }
        </style>
    </head>

    <body>
        <div class="result-section">
            <h1>ผลรวมคะแนน: <?= $totalScore ?></h1>
            <p style="color: <?= $totalScore < 20 ? '#FF9800' : ($totalScore <= 30 ? '#FF5722' : '#F44336') ?>;">
                <?= $totalScore < 20 ? "คุณมีอาการเสพติดการสูบบุหรี่ในเบื้องต้น" :
                    ($totalScore <= 30 ? "คุณมีอาการเสพติดการสูบบุหรี่ในขั้นปานกลาง" :
                        "คุณมีอาการเสพติดการสูบบุหรี่ในขั้นรุนแรง") ?>
            </p>
            <a href="soilder_profile.php" class="back-link"><button>กลับไปยังโปรไฟล์</button></a>
        </div>

        <div class="history-section">
            <h2>ประวัติการทำแบบฟอร์ม</h2>
            <?php
            $historySql = "SELECT a.question_id, a.answer_value, sa.question_text, a.submitted_at
                       FROM answers a
                       JOIN smoking_assessment sa ON a.question_id = sa.question_id
                       WHERE a.soldier_id = $soldier_id
                       ORDER BY a.submitted_at DESC";
            $historyResult = $link->query($historySql);

            if ($historyResult->num_rows > 0) {
                echo "<table><tr><th>คำถาม</th><th>คำตอบ</th><th>วันที่ทำแบบฟอร์ม</th></tr>";
                while ($historyRow = $historyResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($historyRow['question_text']) . "</td>";
                    echo "<td>" . htmlspecialchars($historyRow['answer_value']) . "</td>";
                    echo "<td>" . date('d/m/Y H:i:s', strtotime($historyRow['submitted_at'])) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>ไม่มีประวัติการทำแบบฟอร์ม</p>";
            }
            ?>
        </div>
    </body>

    </html>
    <?php
}
?>