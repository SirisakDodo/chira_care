<?php
require_once '../config/database.php'; // Include database connection
session_start(); // Start session

// Check login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database connection check
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch selected status from dropdown
$selected_status = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

// Fetch soldiers with medical reports based on status filter
$query = "SELECT s.soldier_id, s.soldier_id_card, s.first_name, s.last_name,
                 s.rotation_id, s.training_unit_id, s.affiliated_unit,
                 m.medical_report_id, m.symptom_description, m.status,
                 r.rotation, t.training_unit
          FROM soldier s
          LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
          LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
          LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
          WHERE m.medical_report_id IS NOT NULL";

if ($selected_status) {
    $query .= " AND m.status = ?";
}

$stmt = mysqli_prepare($link, $query);

if ($selected_status) {
    mysqli_stmt_bind_param($stmt, "s", $selected_status);
}

if (!$stmt) {
    die('SQL prepare failed: ' . mysqli_error($link));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die('Query failed: ' . mysqli_error($link));
}

$soldiers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $soldiers[] = $row;
}

mysqli_stmt_close($stmt);

// Process bulk send if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_all'])) {
    $query_all = "SELECT medical_report_id FROM medicalreport WHERE status != 'sent'";
    $result_all = mysqli_query($link, $query_all);

    if ($result_all && mysqli_num_rows($result_all) > 0) {
        while ($row = mysqli_fetch_assoc($result_all)) {
            $medical_report_id = $row['medical_report_id'];
            sendToHospital($medical_report_id);
        }
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลทั้งหมดถูกส่งไปรพ.แล้ว!',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location.reload();
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'ไม่มีข้อมูล',
                    text: 'ไม่มีข้อมูลที่ต้องส่ง',
                    confirmButtonText: 'ตกลง'
                });
            });
        </script>";
    }
}

// Function to update status to 'sent'
function sendToHospital($medical_report_id)
{
    global $link;

    $update_query = "UPDATE medicalreport SET status = 'sent' WHERE medical_report_id = ?";
    $update_stmt = mysqli_prepare($link, $update_query);

    if (!$update_stmt) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'การเตรียมคำสั่ง SQL ล้มเหลว!',
                confirmButtonText: 'ตกลง'
            });
        </script>";
        return;
    }

    mysqli_stmt_bind_param($update_stmt, "i", $medical_report_id);
    $execute_result = mysqli_stmt_execute($update_stmt);

    if (!$execute_result || mysqli_stmt_affected_rows($update_stmt) <= 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด',
                text: 'การส่งข้อมูลล้มเหลว: Medical Report ID = $medical_report_id',
                confirmButtonText: 'ตกลง'
            });
        </script>";
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานอาการทหาร</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <?php include '../components/nav_train.php'; ?>
        <?php include '../components/sidebartrain.php'; ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary mt-4">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลทหารและรายงานอาการ</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="form-group">
                                    <label for="status_filter">กรองตามสถานะ:</label>
                                    <select id="status_filter" name="status_filter" class="form-control">
                                        <option value="" <?php echo $selected_status === '' ? 'selected' : ''; ?>>ทั้งหมด
                                        </option>
                                        <option value="pending" <?php echo $selected_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $selected_status === 'approved' ? 'selected' : ''; ?>>Approved</option>

                                        <option value="sent" <?php echo $selected_status === 'sent' ? 'selected' : ''; ?>>
                                            Sent</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary mb-3">กรอง</button>
                            </form>

                            <form method="POST" action="">
                                <?php if (!empty($soldiers)): ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>รหัสทหาร</th>
                                                <th>ชื่อ</th>
                                                <th>หน่วยฝึกต้นสังกัด</th>
                                                <th>รหัสหมุนเวียน</th>
                                                <th>หน่วยฝึกทหาร</th>
                                                <th>อาการ</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($soldiers as $soldier): ?>
                                                <tr>
                                                    <td><?php echo $soldier['soldier_id_card']; ?></td>
                                                    <td><?php echo $soldier['first_name'] . ' ' . $soldier['last_name']; ?></td>
                                                    <td><?php echo $soldier['affiliated_unit']; ?></td>
                                                    <td><?php echo $soldier['rotation']; ?></td>
                                                    <td><?php echo $soldier['training_unit']; ?></td>
                                                    <td><?php echo nl2br($soldier['symptom_description']); ?></td>
                                                    <td><?php echo $soldier['status']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>ไม่พบข้อมูลที่ต้องส่งในฐานข้อมูล</p>
                                <?php endif; ?>

                                <button type="submit" name="send_all"
                                    class="btn btn-success mt-3">ส่งข้อมูลทั้งหมดไปรพ.</button>
                                <a href="insert_medicalreport.php" class="btn btn-primary mt-3">เพิ่มรายงานป่วย</a>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>