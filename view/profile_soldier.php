<?php
require_once '../config/database.php';

// รับ ID ทหารจาก GET
$soldierId = $_GET['id'] ?? '';

if ($soldierId) {
    $sql = "SELECT
                s.*,
                r.rotation AS rotation_name,
                t.training_unit AS training_unit_name
            FROM
                Soldier s
            LEFT JOIN Rotation r ON s.rotation_id = r.id
            LEFT JOIN Training t ON s.training_unit_id = t.id
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
  <link rel="stylesheet" href="../css/proflie.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include '../components/nav_bar.php'; ?>
  <?php include '../components/sidebar.php'; ?>

  <div class="content-wrapper">
    <div class="profile-card">
      <!-- Image Section -->
      <div class="profile-image">
        <?php if (!empty($soldier['soldier_image'])): ?>
          <img src="data:image/jpeg;base64,<?php echo base64_encode($soldier['soldier_image']); ?>" alt="Soldier Image">
        <?php else: ?>
          <p>ไม่มีรูปภาพ</p>
        <?php endif; ?>
      </div>

      <!-- Details Section -->
      <div class="profile-details">
        <h4><?php echo htmlspecialchars($soldier['first_name'] . ' ' . $soldier['last_name']); ?></h4>
        <ul>
          <li><b>เลขประจำตัวประชาชน:</b> <?php echo htmlspecialchars($soldier['soldier_id_card']); ?></li>
          <li><b>หน่วยฝึก:</b> <?php echo htmlspecialchars($soldier['training_unit_name'] ?? 'ไม่มีข้อมูล'); ?></li>
          <li><b>ผลัด:</b> <?php echo htmlspecialchars($soldier['rotation_name'] ?? 'ไม่มีข้อมูล'); ?></li>
          <li><b>วิธีการคัดเลือก:</b> <?php echo htmlspecialchars($soldier['selection_method'] ?? 'ไม่มีข้อมูล'); ?></li>
          <li><b>ระยะเวลาการฝึก:</b> <?php echo htmlspecialchars($soldier['service_duration'] ?? 'ไม่มีข้อมูล'); ?> เดือน</li>
          <li><b>โรคประจำตัว:</b> <?php echo htmlspecialchars($soldier['underlying_diseases'] ?? '-'); ?></li>
          <li><b>ประวัติแพ้ยา/แพ้อาหาร:</b> <?php echo htmlspecialchars($soldier['allergy_history'] ?? '-'); ?></li>
          <li><b>น้ำหนัก:</b> <?php echo htmlspecialchars($soldier['weight_kg'] ?? '-'); ?> กก.</li>
          <li><b>ส่วนสูง:</b> <?php echo htmlspecialchars($soldier['height_cm'] ?? '-'); ?> ซม.</li>
        </ul>
        <div class="btn-center">
          <a href="edit_soldier.php?id=<?php echo urlencode($soldier['soldier_id_card']); ?>" class="btn btn-primary">แก้ไขข้อมูล</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
