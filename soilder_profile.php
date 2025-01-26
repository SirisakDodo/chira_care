<?php
session_start();
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า user ได้ทำการล็อกอินหรือไม่
if (!isset($_SESSION['user'])) {
    header("Location: soilder_login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จาก session
$user = $_SESSION['user'];
$soldier_id = $user['soldier_id']; // ใช้ soldier_id จาก session

// กำหนดค่าคะแนนเริ่มต้น
$latestScore = "ไม่มีข้อมูลคะแนน";
$latestAlcohol = "ไม่มีข้อมูลคะแนน";
$latestDrug = "ไม่มีข้อมูลคะแนน";
$latestDepression = "ไม่มีข้อมูลคะแนน";
$latestSuicide = "ไม่มีข้อมูลคะแนน";

// ดึงคะแนนจากฐานข้อมูล
// คะแนนการทำแบบฟอร์มบุหรี่
$sql = "SELECT total_score FROM score_history WHERE soldier_id = ? ORDER BY submitted_at DESC LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $soldier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestScore = $row['total_score'];
}

// คะแนนแอลกอฮอล์
$sql = "SELECT total_score FROM alcohol_score_history WHERE soldier_id = ? ORDER BY submitted_at DESC LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $soldier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestAlcohol = $row['total_score'];
}

// คะแนนติดยา
$sql = "SELECT total_score FROM drug_score_history WHERE soldier_id = ? ORDER BY submitted_at DESC LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $soldier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestDrug = $row['total_score'];
}

// คะแนนซึมเศร้า
$sql = "SELECT total_score FROM depression_score_history WHERE soldier_id = ? ORDER BY submitted_at DESC LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $soldier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestDepression = $row['total_score'];
}

// คะแนนฆ่าตัวตาย
$sql = "SELECT total_score FROM suicide_score_history WHERE soldier_id = ? ORDER BY submitted_at DESC LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $soldier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestSuicide = $row['total_score'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">โปรไฟล์ผู้ใช้</h1>
        <div class="card mt-4">
            <div class="card-body">
                <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                <p><strong>เลขบัตรประชาชน:</strong> <?php echo htmlspecialchars($user['soldier_id_card']); ?></p>
                <p><strong>คะแนนการทำแบบฟอร์มบุหรี่:</strong> <?php echo $latestScore; ?></p>
                <p><strong>คะแนนการทำแบบฟอร์มแอลกอฮอล์:</strong> <?php echo $latestAlcohol; ?></p>
                <p><strong>คะแนนการทำแบบฟอร์มติดยา:</strong> <?php echo $latestDrug; ?></p>
                <p><strong>คะแนนการทำแบบฟอร์มซึมเศร้า:</strong> <?php echo $latestDepression; ?></p>
                <p><strong>คะแนนการทำแบบฟอร์มฆ่าตัวตาย:</strong> <?php echo $latestSuicide; ?></p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="form_selection.php" class="btn btn-success">ทำแบบประเมิน</a>
            <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
        </div>
    </div>
</body>
</html>
