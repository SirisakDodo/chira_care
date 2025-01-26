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

$training_unit_id = $_SESSION['user']['training_unit_id']; // ID of the training unit

// Get records per page (default 10)
$records_per_page = isset($_GET['records_per_page']) ? $_GET['records_per_page'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Modified query to fetch only 'pending' status
$query = "SELECT s.soldier_id, s.soldier_id_card, s.first_name, s.last_name,
s.rotation_id, s.training_unit_id, s.affiliated_unit,
m.medical_report_id, m.symptom_description, m.status,
r.rotation, t.training_unit
FROM soldier s
LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
WHERE m.medical_report_id IS NOT NULL AND s.training_unit_id = ? AND m.status = 'pending'
LIMIT ?, ?";

$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "iii", $training_unit_id, $offset, $records_per_page);

if (!$stmt) {
    die('SQL prepare failed: ' . mysqli_error($link));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$soldiers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['status'] = ($row['status'] === 'pending') ? 'ยังไม่ได้นัดหมาย' : $row['status'];
    $soldiers[] = $row;
}

mysqli_stmt_close($stmt);

// Query to get the total number of 'pending' records
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
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'ข้อมูลทั้งหมดถูกส่งไปรพ.แล้ว!',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                window.location.reload();
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'info',
                title: 'ไม่มีข้อมูล',
                text: 'ไม่มีข้อมูลที่ต้องส่ง',
                confirmButtonText: 'ตกลง'
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
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
        }

        .table-wrapper {
            background: #fff;
            padding: 20px;
            margin: 30px 0;
        }

        .table-title {
            padding-bottom: 10px;
            margin: 0 0 10px;
            border-bottom: 2px solid #e9e9e9;
        }

        .table-title h2 {
            margin: 8px 0 0;
            font-size: 24px;
        }

        .table-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .table-filter .btn {
            border: none;
            background: #007bff;
            color: #fff;
            padding: 10px 15px;
            cursor: pointer;
        }

        .table-filter select {
            width: 200px;
            padding: 8px;
            border: 1px solid #ddd;
        }

        table.table {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
        }

        table.table thead th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 12px;
            border: none;
        }

        table.table tbody tr {
            background-color: #fff;
            text-align: center;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            border-radius: 6px;
        }

        table.table tbody tr td {
            padding: 10px 15px;
            border: none;
            vertical-align: middle;
        }

        table.table tbody tr td:last-child {
            text-align: center;
        }

        .action-icons i {
            font-size: 20px;
            margin: 0 5px;
            cursor: pointer;
            color: #007bff;
        }

        .action-icons i:hover {
            color: #0056b3;
        }
    </style>
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
                        <div class="container-xl">
                            <div class="table-responsive">
                                <div class="table-wrapper">
                                    <div class="table-title">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h2>ข้อมูลส่งป่วยทหาร</h2>
                                            </div>
                                            <div class="col-sm-6">
                                                <form method="GET" action="" class="float-right">
                                                    <div class="input-group">


                                                        <!-- ปุ่มค้นหา -->
                                                        <div class="input-group-append mr-2">

                                                        </div>
                                                        <!-- จำนวนรายการต่อหน้า -->
                                                        <select name="records_per_page" class="form-control"
                                                            onchange="this.form.submit()">
                                                            <option value="10" <?= $records_per_page == 10 ? 'selected' : ''; ?>>แสดงผล 10</option>
                                                            <option value="20" <?= $records_per_page == 20 ? 'selected' : ''; ?>>แสดงผล 20</option>
                                                            <option value="30" <?= $records_per_page == 30 ? 'selected' : ''; ?>>แสดงผล 30</option>
                                                            <option value="40" <?= $records_per_page == 40 ? 'selected' : ''; ?>>แสดงผล 40</option>
                                                        </select>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ชื่อ</th>
                                                <th>หน่วยฝึกต้นสังกัด</th>
                                                <th>รหัสหมุนเวียน</th>
                                                <th>หน่วยฝึกทหาร</th>
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
                                                        <td><?php echo $soldier['first_name'] . ' ' . $soldier['last_name']; ?>
                                                        </td>
                                                        <td><?php echo $soldier['affiliated_unit']; ?></td>
                                                        <td><?php echo $soldier['rotation']; ?></td>
                                                        <td><?php echo $soldier['training_unit']; ?></td>
                                                        <td><?php echo nl2br($soldier['symptom_description']); ?></td>
                                                        <td>
                                                            <?php
                                                            $status = $soldier['status'];
                                                            echo $status_mapping[$status] ?? $status;


                                                            ?>
                                                        </td>

                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">ไม่พบข้อมูลที่ต้องส่งในฐานข้อมูล
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div class="clearfix">
                                        <div class="d-flex justify-content-between w-100">
                                            <!-- Left: Send All Button -->
                                            <form method="POST" action="">
                                                <button type="submit" name="send_all"
                                                    class="btn btn-success">ส่งข้อมูลทั้งหมดไปรพ.</button>
                                            </form>

                                            <!-- Center: Pagination -->
                                            <div class="pagination-wrapper d-flex justify-content-center">
                                                <ul class="pagination mb-0">
                                                    <!-- Previous Button -->
                                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                                        <a class="page-link"
                                                            href="?page=<?= $page - 1; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $selected_status; ?>">
                                                            <i class="fas fa-arrow-left"></i> <!-- ไอคอนลูกศร -->
                                                        </a>
                                                    </li>

                                                    <!-- Page Numbers -->
                                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                                            <a class="page-link"
                                                                href="?page=<?= $i; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $selected_status; ?>"><?= $i; ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <!-- Next Button with Icon -->
                                                    <li
                                                        class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                        <a class="page-link"
                                                            href="?page=<?= $page + 1; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $selected_status; ?>">
                                                            <i class="fas fa-arrow-right"></i> <!-- ไอคอนลูกศร -->
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Right: Add Report Button -->
                                            <a href="insert_medicalreport.php"
                                                class="btn btn-primary">เพิ่มรายงานป่วย</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

</body>

</html>