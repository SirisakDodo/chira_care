<?php
require_once 'config.php';

// ตรวจสอบว่ามีพารามิเตอร์ id ถูกส่งมา
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ดึงข้อมูลทหารจากฐานข้อมูล
    $sql = "SELECT s.*,
                   r.id AS rotation_id,
                   r.rotation,
                   r.change_date AS rotation_change_date,
                   t.id AS training_id,
                   t.training_unit,
                   t.change_date AS training_change_date
            FROM Soldier s
            JOIN Rotation r ON s.rotation_id = r.id
            JOIN Training t ON s.training_unit_id = t.id
            WHERE s.id = ?";

    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $soldier = mysqli_fetch_assoc($result);

    // ตรวจสอบว่าพบข้อมูลหรือไม่
    if (!$soldier) {
        die("Soldier not found.");
    }
} else {
    die("ID not provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soldier Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background: white;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, p {
            color: #333;
        }
        .card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }
        .btn-back {
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Soldier Profile</h1>
        <div class="card">
            <p><strong>ID Card:</strong> <?php echo htmlspecialchars($soldier['soldier_id_card']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($soldier['first_name'] . ' ' . $soldier['last_name']); ?></p>
            <p><strong>Rotation ID:</strong> <?php echo htmlspecialchars($soldier['rotation_id']); ?></p>
            <p><strong>Rotation:</strong> <?php echo htmlspecialchars($soldier['rotation']); ?> (Changed on: <?php echo htmlspecialchars($soldier['rotation_change_date']); ?>)</p>
            <p><strong>Training Unit ID:</strong> <?php echo htmlspecialchars($soldier['training_id']); ?></p>
            <p><strong>Training Unit:</strong> <?php echo htmlspecialchars($soldier['training_unit']); ?> (Changed on: <?php echo htmlspecialchars($soldier['training_change_date']); ?>)</p>
            <p><strong>Affiliated Unit:</strong> <?php echo htmlspecialchars($soldier['affiliated_unit']); ?></p>
            <p><strong>Weight:</strong> <?php echo htmlspecialchars($soldier['weight_kg']); ?> kg</p>
            <p><strong>Height:</strong> <?php echo htmlspecialchars($soldier['height_cm']); ?> cm</p>
            <p><strong>Medical History:</strong> <?php echo htmlspecialchars($soldier['medical_allergy_food_history']); ?></p>
            <p><strong>Underlying Diseases:</strong> <?php echo htmlspecialchars($soldier['underlying_diseases']); ?></p>
        </div>
        <a href="index.php" class="btn-back">Back</a>
    </div>
</body>
</html>
