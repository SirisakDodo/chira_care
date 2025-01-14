<?php
require_once '../config/database.php';

$soldierId = $_GET['id'] ?? '';

if ($soldierId) {
    $sql = "SELECT * FROM Soldier WHERE soldier_id_card = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $soldierId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $soldier = mysqli_fetch_assoc($result);

    if (!$soldier) {
        echo "<script>alert('ไม่พบข้อมูลทหาร'); window.location.href = 'index.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ไม่พบข้อมูลทหาร'); window.location.href = 'index.php';</script>";
    exit;
}

// Fetch available rotations
$sqlRotation = "SELECT * FROM Rotation";
$resultRotation = mysqli_query($link, $sqlRotation);

// Fetch available training units
$sqlTraining = "SELECT * FROM Training";
$resultTraining = mysqli_query($link, $sqlTraining);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Soldier Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    .content-wrapper {
      background-color: #f4f6f9;
      padding: 20px;
    }
    .card {
      margin: 20px auto;
      max-width: 800px;
    }
    .card-header {
      background-color: #28a745;
      color: #fff;
    }
    .form-group img {
      display: block;
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .btn-save {
      width: 100%;
      margin-top: 20px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include '../components/nav_bar.php'; ?>
  <?php include '../components/sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>แก้ไขข้อมูลทหาร</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">แก้ไขข้อมูลทหาร</h3>
          </div>
          <div class="card-body">
            <form action="update_soldier.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?php echo htmlspecialchars($soldier['soldier_id_card']); ?>">

              <!-- Basic Information -->
              <div class="form-group">
                <label>ชื่อ:</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($soldier['first_name']); ?>">
              </div>
              <div class="form-group">
                <label>นามสกุล:</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($soldier['last_name']); ?>">
              </div>

              <!-- Rotation Selection -->
              <div class="form-group">
                <label>ผลัด:</label>
                <select name="rotation_id" class="form-control">
                  <?php while ($rotation = mysqli_fetch_assoc($resultRotation)): ?>
                    <option value="<?php echo $rotation['id']; ?>" <?php echo $soldier['rotation_id'] == $rotation['id'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($rotation['rotation']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <!-- Training Unit Selection -->
              <div class="form-group">
                <label>หน่วยฝึก:</label>
                <select name="training_unit_id" class="form-control">
                  <?php while ($training = mysqli_fetch_assoc($resultTraining)): ?>
                    <option value="<?php echo $training['id']; ?>" <?php echo $soldier['training_unit_id'] == $training['id'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($training['training_unit']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <!-- Affiliated Unit Selection (New Field) -->
              <div class="form-group">
                <label>หน่วยสังกัด:</label>
                <input type="text" name="affiliated_unit" class="form-control" value="<?php echo htmlspecialchars($soldier['affiliated_unit']); ?>">
              </div>

              <!-- Health and Physical Info -->
              <div class="form-group">
                <label>น้ำหนัก (กก.):</label>
                <input type="text" name="weight_kg" class="form-control" value="<?php echo htmlspecialchars($soldier['weight_kg']); ?>">
              </div>
              <div class="form-group">
                <label>ส่วนสูง (ซม.):</label>
                <input type="text" name="height_cm" class="form-control" value="<?php echo htmlspecialchars($soldier['height_cm']); ?>">
              </div>
              <div class="form-group">
                <label>ประวัติแพ้ยา/แพ้อาหาร:</label>
                <textarea name="medical_allergy_food_history" class="form-control"><?php echo htmlspecialchars($soldier['medical_allergy_food_history']); ?></textarea>
              </div>
              <div class="form-group">
                <label>โรคประจำตัว:</label>
                <textarea name="underlying_diseases" class="form-control"><?php echo htmlspecialchars($soldier['underlying_diseases']); ?></textarea>
              </div>

              <!-- Other Details -->
              <div class="form-group">
                <label>วิธีการคัดเลือก:</label>
                <input type="text" name="selection_method" class="form-control" value="<?php echo htmlspecialchars($soldier['selection_method']); ?>">
              </div>
              <div class="form-group">
                <label>ระยะเวลาการฝึก (เดือน):</label>
                <input type="text" name="service_duration" class="form-control" value="<?php echo htmlspecialchars($soldier['service_duration']); ?>">
              </div>

              <!-- Image Upload -->
              <div class="form-group">
                <label>อัปโหลดรูปภาพ:</label>
                <input type="file" name="soldier_image" class="form-control">
                <?php if (!empty($soldier['soldier_image'])): ?>
                  <img src="data:image/jpeg;base64,<?php echo base64_encode($soldier['soldier_image']); ?>" class="img-thumbnail mt-3">
                <?php endif; ?>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn btn-success btn-save">บันทึกข้อมูล</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
</body>
</html>
