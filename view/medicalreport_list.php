<?php
require_once '../config/database.php'; // เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ตั้งค่าการแบ่งหน้า
$records_per_page = isset($_GET['records_per_page']) ? (int) $_GET['records_per_page'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// รับค่าการกรองสถานะจากฟอร์ม
$status_filter = isset($_GET['status_filter']) && in_array($_GET['status_filter'], ['approved', 'sent']) ? $_GET['status_filter'] : '';

// สร้างเงื่อนไขการกรองตามสถานะ
$status_condition = '';
if ($status_filter) {
    $status_condition = "AND m.status = ?";
}

$query = "SELECT s.soldier_id, s.soldier_id_card, s.first_name, s.last_name,
                 s.rotation_id, s.training_unit_id, s.affiliated_unit,
                 m.medical_report_id, m.symptom_description, m.status,
                 r.rotation, t.training_unit,
                 a.appointment_date, a.appointment_location
          FROM soldier s
          LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
          LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
          LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
          LEFT JOIN medicalreportapproval a ON m.medical_report_id = a.medical_report_id
          WHERE m.medical_report_id IS NOT NULL
          AND s.training_unit_id = ?
          AND m.status IN ('sent', 'approved')
          $status_condition
          LIMIT ?, ?";


$stmt = mysqli_prepare($link, $query);
if (!$stmt) {
    die("SQL Prepare Failed: " . mysqli_error($link));
}

// Bind parameters based on filter condition
if ($status_filter) {
    mysqli_stmt_bind_param($stmt, "isii", $_SESSION['user']['training_unit_id'], $status_filter, $offset, $records_per_page);
} else {
    mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user']['training_unit_id'], $offset, $records_per_page);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$soldiers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $soldiers[] = $row;
}
mysqli_stmt_close($stmt);

// คำนวณจำนวนหน้าทั้งหมด
$total_query = "SELECT COUNT(*) as total
                FROM soldier s
                LEFT JOIN medicalreport m ON s.soldier_id = m.soldier_id
                WHERE m.medical_report_id IS NOT NULL
                AND s.training_unit_id = ?
                $status_condition";

$total_stmt = mysqli_prepare($link, $total_query);

if ($status_filter) {
    mysqli_stmt_bind_param($total_stmt, "is", $_SESSION['user']['training_unit_id'], $status_filter);
} else {
    mysqli_stmt_bind_param($total_stmt, "i", $_SESSION['user']['training_unit_id']);
}

mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);
mysqli_stmt_close($total_stmt);
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
                                                        <form method="GET" action="" class="float-right">
                                                            <div class="input-group">
                                                                <!-- Status Filter -->
                                                                <select name="status_filter" class="form-control"
                                                                    onchange="this.form.submit()">
                                                                    <option value="" <?= empty($_GET['status_filter']) ? 'selected' : ''; ?>>ทั้งหมด</option>
                                                                    <option value="sent"
                                                                        <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'sent') ? 'selected' : ''; ?>>รอนัดหมาย</option>
                                                                    <option value="approved"
                                                                        <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'approved') ? 'selected' : ''; ?>>นัดหมายเรียบร้อย</option>
                                                                </select>

                                                                <!-- Records Per Page -->
                                                                <select name="records_per_page"
                                                                    class="form-control ml-2"
                                                                    onchange="this.form.submit()">
                                                                    <option value="10" <?= $records_per_page == 10 ? 'selected' : ''; ?>>แสดงผล 10</option>
                                                                    <option value="20" <?= $records_per_page == 20 ? 'selected' : ''; ?>>แสดงผล 20</option>
                                                                    <option value="30" <?= $records_per_page == 30 ? 'selected' : ''; ?>>แสดงผล 30</option>
                                                                    <option value="40" <?= $records_per_page == 40 ? 'selected' : ''; ?>>แสดงผล 40</option>
                                                                </select>
                                                            </div>
                                                        </form>

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
                                                            $status_mapping = [
                                                                'approved' => '<span class="badge badge-success">นัดหมายเรียบร้อย</span>',
                                                                'sent' => '<span class="badge badge-warning">รอนัดหมาย</span>'
                                                            ];
                                                            echo $status_mapping[$status] ?? '<span class="badge badge-secondary">ไม่ทราบสถานะ</span>';

                                                            if ($status === 'approved') {
                                                                $appointment_date = isset($soldier['appointment_date'])
                                                                    ? date('d-m-Y H:i', strtotime($soldier['appointment_date']))
                                                                    : 'N/A';

                                                                $appointment_location = isset($soldier['appointment_location'])
                                                                    ? $soldier['appointment_location']
                                                                    : 'N/A';

                                                                echo "<br><strong>วันที่และเวลา:</strong> " . $appointment_date;
                                                                echo "<br><strong>สถานที่:</strong> " . $appointment_location;
                                                            }

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

                                        <!-- Center: Pagination -->
                                        <div class="d-flex justify-content-center">
                                            <ul class="pagination mb-0 text-center">
                                                <!-- Previous Button -->
                                                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                                    <a class="page-link"
                                                        href="?page=<?= $page - 1; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $status_filter; ?>">
                                                        <i class="fas fa-arrow-left"></i> <!-- ไอคอนลูกศร -->
                                                    </a>
                                                </li>

                                                <!-- Page Numbers -->
                                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                                        <a class="page-link"
                                                            href="?page=<?= $i; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $status_filter; ?>"><?= $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <!-- Next Button with Icon -->
                                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                    <a class="page-link"
                                                        href="?page=<?= $page + 1; ?>&records_per_page=<?= $records_per_page; ?>&status_filter=<?= $status_filter; ?>">
                                                        <i class="fas fa-arrow-right"></i> <!-- ไอคอนลูกศร -->
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>



                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

</body>

</html>