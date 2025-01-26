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

$training_unit_id = $_SESSION['user']['training_unit_id'];

// Get records per page (default 10)
$records_per_page = isset($_GET['records_per_page']) ? $_GET['records_per_page'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch only records with 'pending' status
$query = "SELECT s.soldier_id, s.first_name, s.last_name,
m.medical_report_id, m.symptom_description, m.status,
t.training_unit
FROM soldier s
LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
WHERE m.medical_report_id IS NOT NULL AND s.training_unit_id = ? AND m.status = 'pending'
LIMIT ?, ?";

$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "iii", $training_unit_id, $offset, $records_per_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$soldiers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $soldiers[] = $row;
}
mysqli_stmt_close($stmt);

// Query to get the total number of records for pagination
$total_query = "SELECT COUNT(*) as total FROM soldier s
LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
WHERE m.medical_report_id IS NOT NULL AND s.training_unit_id = ? AND m.status = 'pending'";

$total_stmt = mysqli_prepare($link, $total_query);
mysqli_stmt_bind_param($total_stmt, "i", $training_unit_id);
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($total_stmt);

// Process bulk send if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_all'])) {
    $query_all = "SELECT medical_report_id FROM medicalreport WHERE status = 'pending'";
    $result_all = mysqli_query($link, $query_all);

    if ($result_all && mysqli_num_rows($result_all) > 0) {
        while ($row = mysqli_fetch_assoc($result_all)) {
            $medical_report_id = $row['medical_report_id'];
            sendToHospital($medical_report_id);
        }
    }
    // Redirect to avoid displaying any confirmation message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Function to update status to 'sent'
function sendToHospital($medical_report_id)
{
    global $link;
    $update_query = "UPDATE medicalreport SET status = 'sent' WHERE medical_report_id = ?";
    $update_stmt = mysqli_prepare($link, $update_query);
    mysqli_stmt_bind_param($update_stmt, "i", $medical_report_id);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานอาการทหาร</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
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
                            <h3 class="card-title">ข้อมูลทหารที่ยังไม่นัดหมาย</h3>
                        </div>
                        <div class="container-xl">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อ</th>
                                            <th>หน่วยฝึก</th>
                                            <th>อาการ</th>
                                            <th>สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($soldiers)): ?>
                                            <?php $count = 1; ?>
                                            <?php foreach ($soldiers as $soldier): ?>
                                                <tr>
                                                    <td><?php echo $count++; ?></td>
                                                    <td><?php echo $soldier['first_name'] . ' ' . $soldier['last_name']; ?></td>
                                                    <td><?php echo $soldier['training_unit']; ?></td>
                                                    <td><?php echo nl2br($soldier['symptom_description']); ?></td>
                                                    <td>ยังไม่นัดหมาย</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="clearfix">
                                    <div class="d-flex justify-content-between">
                                        <form method="POST" action="">
                                            <button type="submit" name="send_all" class="btn btn-success">ส่งข้อมูลทั้งหมดไปรพ.</button>
                                        </form>

                                        <div class="pagination-wrapper">
                                            <ul class="pagination">
                                                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                                    <a class="page-link" href="?page=<?= $page - 1; ?>">ก่อนหน้า</a>
                                                </li>
                                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                    <a class="page-link" href="?page=<?= $page + 1; ?>">ถัดไป</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </section>
        </div>
    </div>
</body>
</html>
