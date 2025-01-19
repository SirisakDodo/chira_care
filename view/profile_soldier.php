<?php
require_once '../config/database.php';

// Receive soldier ID from GET
$soldierId = $_GET['id'] ?? '';

if ($soldierId) {
    $sql = "SELECT
                s.*,
                r.rotation AS rotation_name,
                t.training_unit AS training_unit_name
            FROM
                Soldier s
            LEFT JOIN Rotation r ON s.rotation_id = r.rotation_id
            LEFT JOIN Training t ON s.training_unit_id = t.training_unit_id
            WHERE s.soldier_id_card = ?";

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $soldierId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $soldier = mysqli_fetch_assoc($result);

    if (!$soldier) {
        echo "<script>alert('ไม่พบข้อมูลทหาร'); window.location.href = 'index.php';</script>";
        exit;
    }

    // BMI Calculation
    $weight = $soldier['weight_kg'] ?? 0;
    $height = $soldier['height_cm'] ?? 0;
    $bmi = ($height > 0) ? round($weight / (($height / 100) ** 2), 2) : 'ไม่มีข้อมูล';
} else {
    echo "<script>alert('ไม่พบข้อมูลทหาร'); window.location.href = 'index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ข้อมูลทหาร</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .profile-card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .profile-card {
            display: flex;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f5f5f5;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-details {
            flex: 1;
            margin-right: 20px;
        }

        .profile-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .profile-column {
            flex: 1;
            padding: 0 10px;
        }

        .profile-label {
            font-weight: bold;
            margin-right: 5px;
        }

        /* ชื่อ-นามสกุลขนาดใหญ่ */
        .name-column {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }

        .profile-image {
            flex-shrink: 0;
            text-align: center;
        }

        .profile-image img {
            width: 150px;
            height: 150px;
            border-radius: 5px;
            border: 2px solid #555;
            object-fit: cover;
        }

        .btn-center {
            margin-top: 20px;
            text-align: left;
        }

        .btn {
            padding: 8px 15px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include '../components/nav_bar.php'; ?>
        <?php include '../components/sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="profile-card-container">
                <div class="profile-card">
                    <!-- Soldier Details -->
                    <div class="profile-details">
                        <div class="profile-row">
                            <div class="profile-column name-column">
                                <?php echo htmlspecialchars($soldier['first_name'] . ' ' . $soldier['last_name']); ?>
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">วิธีการคัดเลือก:</span>
                                <?php echo htmlspecialchars($soldier['selection_method'] ?? 'ไม่มีข้อมูล'); ?>
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">ผลัด:</span>
                                <?php echo htmlspecialchars($soldier['rotation_name'] ?? 'ไม่มีข้อมูล'); ?>
                            </div>
                        </div>

                        <div class="profile-row">
                            <div class="profile-column">
                                <span class="profile-label">เลขประจำตัวประชาชน:</span>
                                <?php echo htmlspecialchars($soldier['soldier_id_card']); ?>
                            </div>
                        </div>

                        <div class="profile-row">
                            <div class="profile-column">
                                <span class="profile-label">หน่วยฝึก:</span>
                                <?php echo htmlspecialchars($soldier['training_unit_name'] ?? 'ไม่มีข้อมูล'); ?>
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">ระยะเวลาการฝึก:</span>
                                <?php echo htmlspecialchars($soldier['service_duration'] ?? 'ไม่มีข้อมูล'); ?> เดือน
                            </div>
                        </div>

                        <div class="profile-row">
                            <div class="profile-column">
                                <span class="profile-label">โรคประจำตัว:</span>
                                <?php echo htmlspecialchars($soldier['underlying_diseases'] ?? '-'); ?>
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">ประวัติแพ้ยา/แพ้อาหาร:</span>
                                <?php echo htmlspecialchars($soldier['medical_allergy_food_history'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="profile-row">
                            <div class="profile-column">
                                <span class="profile-label">น้ำหนัก:</span>
                                <?php echo htmlspecialchars($soldier['weight_kg'] ?? '-'); ?> กก.
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">ส่วนสูง:</span>
                                <?php echo htmlspecialchars($soldier['height_cm'] ?? '-'); ?> ซม.
                            </div>
                            <div class="profile-column">
                                <span class="profile-label">BMI:</span>
                                <?php echo htmlspecialchars($bmi); ?>
                            </div>
                        </div>

                        <div class="btn-center">
                            <a href="edit_soldier.php?id=<?php echo urlencode($soldier['soldier_id_card']); ?>"
                                class="btn btn-primary">แก้ไขข้อมูล</a>
                        </div>
                    </div>

                    <!-- Soldier Image -->
                    <div class="profile-image">
                        <?php if (!empty($soldier['soldier_image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($soldier['soldier_image']); ?>"
                                alt="Soldier Image">
                        <?php else: ?>
                            <p>ไม่มีรูปภาพ</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>