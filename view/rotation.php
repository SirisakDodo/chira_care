<?php
require_once '../config/database.php';

// Add Rotation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rotation'])) {
  $rotation = mysqli_real_escape_string($link, $_POST['rotation']);
  $sql = "INSERT INTO Rotation (rotation) VALUES (?)";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "s", $rotation);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

// Add Training Unit and create user in militaryusers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_training'])) {
  $training_unit = mysqli_real_escape_string($link, $_POST['training_unit']);
  
  // Insert into Training table
  $sql = "INSERT INTO Training (training_unit) VALUES (?)";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "s", $training_unit);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  
  // Get the training_unit_id of the newly added training unit
  $training_unit_id = mysqli_insert_id($link);
  
  // Now, Insert into militaryusers table, with training_unit_id and '1234' as password
  $password = password_hash("1234", PASSWORD_DEFAULT); // Hashing the password for security
  $sql_user = "INSERT INTO militaryusers (training_unit_id, password) VALUES (?, ?)";
  $stmt_user = mysqli_prepare($link, $sql_user);
  mysqli_stmt_bind_param($stmt_user, "is", $training_unit_id, $password); // Bind both training_unit_id and password
  mysqli_stmt_execute($stmt_user);
  mysqli_stmt_close($stmt_user);
}


// Delete Rotation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_rotation'])) {
  $rotation_id = intval($_POST['rotation_id']); // ตรวจสอบค่า rotation_id ที่รับจากฟอร์ม
  // ใช้ rotation_id ในการลบข้อมูล
  $sql = "DELETE FROM rotation WHERE rotation_id = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "i", $rotation_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

// Delete Training Unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_training'])) {
  $training_id = intval($_POST['training_id']); // รับค่า training_id จากฟอร์ม
  // ใช้ training_unit_id เพื่อทำการลบข้อมูล
  $sql = "DELETE FROM training WHERE training_unit_id = ?";
  $stmt = mysqli_prepare($link, $sql);
  mysqli_stmt_bind_param($stmt, "i", $training_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}


// Retrieve Rotation
$rotationQuery = "SELECT * FROM Rotation";
$rotationResult = mysqli_query($link, $rotationQuery);

// Retrieve Training
$trainingQuery = "SELECT * FROM Training";
$trainingResult = mysqli_query($link, $trainingQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Starter</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <?php include '../components/nav_bar.php'; ?>
    <?php include '../components/sidebar.php'; ?>

    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid">
          <header>
            <h1>Military</h1>
          </header>
          <main>
            <div class="form-container">
              <div class="form-row">
                <!-- Add Rotation Form -->
                <div class="form-col">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">เพิ่มผลัด</h3>
                    </div>
                    <div class="card-body">
                      <form method="POST" action="">
                        <div class="form-group">
                          <label for="rotation">ผลัด</label>
                          <input type="text" class="form-control" id="rotation" name="rotation" placeholder="Enter Rotation" required>
                        </div>
                        <button type="submit" name="add_rotation" class="btn btn-primary">เพิ่ม</button>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Add Training Form -->
                <div class="form-col">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">เพิ่มหน่วยฝึก</h3>
                    </div>
                    <div class="card-body">
                      <form method="POST" action="">
                        <div class="form-group">
                          <label for="training_unit">หน่วยฝึก</label>
                          <input type="text" class="form-control" id="training_unit" name="training_unit" placeholder="Enter Training Unit" required>
                        </div>
                        <button type="submit" name="add_training" class="btn btn-primary">เพิ่ม</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Display Rotation and Training Lists -->
              <div class="table-container">
                <!-- Rotation List -->
                <div class="table-card">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">รายชื่อผลัด</h3>
                    </div>
                    <div class="card-body">
                      <table>
                        <thead>
                          <tr>
                            <th>ผลัด</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = mysqli_fetch_assoc($rotationResult)) { ?>
                            <tr>
                              <td>
                                <?php echo htmlspecialchars($row['rotation']); ?>
                                <form method="POST" action="" style="display:inline-block; float:right;">
                                  <input type="hidden" name="rotation_id" value="<?php echo $row['rotation_id']; ?>"> <!-- ตรวจสอบให้แน่ใจว่าใช้ชื่อคอลัมน์ที่ถูกต้อง -->
                                  <button type="submit" name="delete_rotation" class="btn btn-danger btn-sm">ลบ</button>
                                </form>

                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Training List -->
                <div class="table-card">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">รายชื่อหน่วยฝึก</h3>
                    </div>
                    <div class="card-body">
                      <table>
                        <thead>
                          <tr>
                            <th>หน่วยฝึก</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = mysqli_fetch_assoc($trainingResult)) { ?>
                            <tr>
                              <td>
                                <?php echo htmlspecialchars($row['training_unit']); ?>
                                <form method="POST" action="" style="display:inline-block; float:right;">
                                  <input type="hidden" name="training_id" value="<?php echo $row['training_unit_id']; ?>"> <!-- ใช้ training_unit_id -->
                                  <button type="submit" name="delete_training" class="btn btn-danger btn-sm">ลบ</button>
                                </form>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </main>
        </div>
      </section>
    </div>

    <?php include '../components/footer.php'; ?>

  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>