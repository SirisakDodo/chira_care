<?php
require_once __DIR__ . '/../../config/database.php';

// Handle Add Rotation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_rotation'])) {
    $rotation_name = mysqli_real_escape_string($link, $_POST['rotation_name']);
    $query = "INSERT INTO rotation (rotation, rotation_status) VALUES ('$rotation_name', 'active')";
    mysqli_query($link, $query);
    header("Location: rotation_training.php");
    exit();
}

// Handle Add Training
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_training'])) {
    $training_name = mysqli_real_escape_string($link, $_POST['training_name']);
    $query = "INSERT INTO training (training_unit, training_status) VALUES ('$training_name', 'active')";
    mysqli_query($link, $query);
    header("Location: rotation_training.php");
    exit();
}

// Handle Change Status
if (isset($_GET['toggle_status']) && isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']);
    $query = "UPDATE $table SET " . $table . "_status = IF(" . $table . "_status = 'active', 'inactive', 'active') WHERE " . $table . "_id = $id";
    mysqli_query($link, $query);
    header("Location: rotation_training.php");
    exit();
}

if (isset($_GET['delete']) && isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']);

    // ตรวจสอบค่าที่รับมา
    echo "<script>console.log('Delete Request: Table = $table, ID = $id');</script>";

    $column = ($table == "rotation") ? "rotation_id" : "training_unit_id";
    $check_query = "SELECT COUNT(*) AS total FROM soldier WHERE $column = $id";
    $check_result = mysqli_query($link, $check_query);

    if (!$check_result) {
        die("<script>alert('SQL Error: " . mysqli_error($link) . "'); window.location.href = 'rotation_training.php';</script>");
    }

    $row = mysqli_fetch_assoc($check_result);

    if ($row['total'] > 0) {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากยังมีทหารอยู่ในรายการนี้'); window.location.href = 'rotation_training.php';</script>";
    } else {
        $delete_query = "DELETE FROM $table WHERE " . $table . "_id = $id";
        $delete_result = mysqli_query($link, $delete_query);

        if (!$delete_result) {
            die("<script>alert('Error in DELETE: " . mysqli_error($link) . "'); window.location.href = 'rotation_training.php';</script>");
        }

        echo "<script>alert('ลบข้อมูลสำเร็จ!'); window.location.href = 'rotation_training.php';</script>";
    }
}


// Fetch Data
$query_rotation = "SELECT * FROM rotation";
$result_rotation = mysqli_query($link, $query_rotation);

$query_training = "SELECT * FROM training";
$result_training = mysqli_query($link, $query_training);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>Manage Rotation & Training</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center">จัดการ Rotation & Training</h2>

        <div class="row">
            <div class="col-md-6">
                <!-- Form Add Rotation -->
                <div class="card shadow p-3">
                    <h5>เพิ่มผลัด (Rotation)</h5>
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" name="rotation_name" class="form-control" placeholder="ชื่อผลัด"
                                required>
                            <button type="submit" name="add_rotation" class="btn btn-primary">เพิ่ม</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Form Add Training -->
                <div class="card shadow p-3">
                    <h5>เพิ่มหน่วยฝึก (Training)</h5>
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" name="training_name" class="form-control" placeholder="ชื่อหน่วยฝึก"
                                required>
                            <button type="submit" name="add_training" class="btn btn-primary">เพิ่ม</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <hr>

        <h3 class="mt-4">รายการ Rotation</h3>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ชื่อผลัด</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_rotation)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(string: $row['rotation']); ?></td>
                        <td>
                            <span
                                class="badge <?php echo ($row['rotation_status'] == 'active') ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo htmlspecialchars($row['rotation_status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="?toggle_status=1&table=rotation&id=<?php echo $row['rotation_id']; ?>"
                                class="btn btn-sm btn-warning">
                                เปลี่ยนสถานะ
                            </a>
                            <a href="?delete=1&table=rotation&id=<?php echo $row['rotation_id']; ?>"
                                class="btn btn-sm btn-danger" onclick="return confirm('ต้องการลบใช่หรือไม่?')">ลบ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3 class="mt-4">รายการ Training</h3>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ชื่อหน่วยฝึก</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_training)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['training_unit']); ?></td>
                        <td>
                            <span
                                class="badge <?php echo ($row['training_status'] == 'active') ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo htmlspecialchars($row['training_status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="?toggle_status=1&table=training&id=<?php echo $row['training_unit_id']; ?>"
                                class="btn btn-sm btn-warning">
                                เปลี่ยนสถานะ
                            </a>
                            <a href="?delete=1&table=training&id=<?php echo $row['training_unit_id']; ?>"
                                class="btn btn-sm btn-danger" onclick="return confirm('ต้องการลบใช่หรือไม่?')">ลบ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>