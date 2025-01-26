<<<<<<< HEAD
<?php
session_start();  // เริ่มต้น session
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงคำถามทั้งหมดจากฐานข้อมูลเพื่อวน loop
    $sql = "SELECT question_id FROM suicide_assessment";
    $result = $link->query($sql);

    $totalScore = 0; // เก็บคะแนนรวม
    $soldier_id = $_SESSION['user']['soldier_id']; // ดึง soldier_id จาก session


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            $answer_value = isset($_POST["answer_$question_id"]) ? intval($_POST["answer_$question_id"]) : 0;

            // รวมคะแนนจากคำตอบ
            $totalScore += $answer_value;
        }
    }

    // บันทึกคะแนนรวมลงในตาราง score_history
    $insertScoreSql = "INSERT INTO suicide_score_history (soldier_id, total_score, submitted_at)
                       VALUES (?, ?, NOW())";
    $stmt = $link->prepare($insertScoreSql);
    $stmt->bind_param("ii", $soldier_id, $totalScore);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการประเมิน</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">ผลรวมคะแนน: <?php echo $totalScore; ?></h1>
        <p class="text-center">
            <?php
            if ($totalScore < 20) {
                echo "<span class='text-warning'>คุณมีอาการอยากฆ่าตัวตายในเบื้องต้น</span>";
            } elseif ($totalScore >= 20 && $totalScore <= 30) {
                echo "<span class='text-info'>คุณมีอาการอยากฆ่าตัวตายในขั้นปานกลาง</span>";
            } else {
                echo "<span class='text-danger'>คุณมีอาการอยากฆ่าตัวตายในขั้นรุนแรง</span>";
            }
            ?>
        </p>
        <div class="text-center mt-4">
            <a href="soilder_profile.php" class="btn btn-secondary">กลับไปยังโปรไฟล์</a>
        </div>
        <div class="mt-5">
            <h2>ประวัติการทำแบบฟอร์ม</h2>
            <?php
            // ดึงประวัติการทำแบบฟอร์มจากฐานข้อมูล
            $historySql = "SELECT total_score, submitted_at
                           FROM suicide_score_history
                           WHERE soldier_id = ?
                           ORDER BY submitted_at DESC";

            $stmt = $link->prepare($historySql);
            $stmt->bind_param("i", $soldier_id);
            $stmt->execute();
            $historyResult = $stmt->get_result();

            if ($historyResult->num_rows > 0) {
                echo "<table class='table table-striped mt-3'>";
                echo "<thead class='table-dark'><tr><th>คะแนนรวม</th><th>วันที่ทำแบบฟอร์ม</th></tr></thead><tbody>";
                while ($historyRow = $historyResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $historyRow['total_score'] . "</td>";
                    echo "<td>" . $historyRow['submitted_at'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-center'>ไม่มีประวัติการทำแบบฟอร์ม</p>";
            }
            ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
=======
<?php
session_start();  // เริ่มต้น session
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงคำถามทั้งหมดจากฐานข้อมูลเพื่อวน loop
    $sql = "SELECT question_id FROM suicide_assessment";
    $result = $link->query($sql);

    $totalScore = 0; // เก็บคะแนนรวม
    $soldier_id = $_SESSION['user']['soldier_id']; // ดึง soldier_id จาก session


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            $answer_value = isset($_POST["answer_$question_id"]) ? intval($_POST["answer_$question_id"]) : 0;

            // รวมคะแนนจากคำตอบ
            $totalScore += $answer_value;
        }
    }

    // บันทึกคะแนนรวมลงในตาราง score_history
    $insertScoreSql = "INSERT INTO suicide_score_history (soldier_id, total_score, submitted_at) 
                       VALUES (?, ?, NOW())";
    $stmt = $link->prepare($insertScoreSql);
    $stmt->bind_param("ii", $soldier_id, $totalScore);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลการประเมิน</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">ผลรวมคะแนน: <?php echo $totalScore; ?></h1>
        <p class="text-center">
            <?php
            if ($totalScore < 20) {
                echo "<span class='text-warning'>คุณมีอาการอยากฆ่าตัวตายในเบื้องต้น</span>";
            } elseif ($totalScore >= 20 && $totalScore <= 30) {
                echo "<span class='text-info'>คุณมีอาการอยากฆ่าตัวตายในขั้นปานกลาง</span>";
            } else {
                echo "<span class='text-danger'>คุณมีอาการอยากฆ่าตัวตายในขั้นรุนแรง</span>";
            }
            ?>
        </p>
        <div class="text-center mt-4">
            <a href="soilder_profile.php" class="btn btn-secondary">กลับไปยังโปรไฟล์</a>
        </div>
        <div class="mt-5">
            <h2>ประวัติการทำแบบฟอร์ม</h2>
            <?php
            // ดึงประวัติการทำแบบฟอร์มจากฐานข้อมูล
            $historySql = "SELECT total_score, submitted_at
                           FROM suicide_score_history
                           WHERE soldier_id = ?
                           ORDER BY submitted_at DESC";

            $stmt = $link->prepare($historySql);
            $stmt->bind_param("i", $soldier_id);
            $stmt->execute();
            $historyResult = $stmt->get_result();

            if ($historyResult->num_rows > 0) {
                echo "<table class='table table-striped mt-3'>";
                echo "<thead class='table-dark'><tr><th>คะแนนรวม</th><th>วันที่ทำแบบฟอร์ม</th></tr></thead><tbody>";
                while ($historyRow = $historyResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $historyRow['total_score'] . "</td>";
                    echo "<td>" . $historyRow['submitted_at'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-center'>ไม่มีประวัติการทำแบบฟอร์ม</p>";
            }
            ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
>>>>>>> 466ce0eae4b728a5573c253d86c02fd0dcbef446
