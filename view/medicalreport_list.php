<?php
require_once '../config/database.php'; // เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// ตั้งค่าการแบ่งหน้า
$records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// รับค่าการค้นหาจากผู้ใช้
$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";

// คำสั่ง SQL เพื่อดึงข้อมูลที่ต้องการ
$query = "SELECT 
            s.first_name, 
            s.last_name, 
            t.training_unit, 
            m.symptom_description, 
            m.status 
          FROM medicalreport m
          JOIN soldier s ON m.soldier_id = s.soldier_id
          JOIN training t ON s.training_unit_id = t.training_unit_id
          WHERE 1";

if (!empty($search_query)) {
    $query .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR m.symptom_description LIKE ? OR m.status LIKE ?)";
}

$query .= " LIMIT ?, ?";
$stmt = mysqli_prepare($link, $query);

if (!empty($search_query)) {
    $search_term = "%{$search_query}%";
    mysqli_stmt_bind_param($stmt, "ssssii", $search_term, $search_term, $search_term, $search_term, $offset, $records_per_page);
} else {
    mysqli_stmt_bind_param($stmt, "ii", $offset, $records_per_page);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$reports = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reports[] = $row;
}
mysqli_stmt_close($stmt);

// นับจำนวนข้อมูลทั้งหมดสำหรับการแบ่งหน้า
$count_query = "SELECT COUNT(*) as total 
                FROM medicalreport m
                JOIN soldier s ON m.soldier_id = s.soldier_id
                JOIN training t ON s.training_unit_id = t.training_unit_id
                WHERE 1";

if (!empty($search_query)) {
    $count_query .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR m.symptom_description LIKE ? OR m.status LIKE ?)";
}

$total_stmt = mysqli_prepare($link, $count_query);

if (!empty($search_query)) {
    mysqli_stmt_bind_param($total_stmt, "ssss", $search_term, $search_term, $search_term, $search_term);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">รายการรายงานอาการทหาร</h2>

        <form method="GET" action="" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อทหาร หน่วยฝึก อาการ สถานะ" value="<?= htmlspecialchars($search_query) ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">ค้นหา</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อทหาร</th>
                    <th>หน่วยฝึก</th>
                    <th>อาการ</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php $count = $offset + 1; ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($report['first_name']) . ' ' . htmlspecialchars($report['last_name']) ?></td>
                            <td><?= htmlspecialchars($report['training_unit']) ?></td>
                            <td><?= nl2br(htmlspecialchars($report['symptom_description'])) ?></td>
                            <td><?= htmlspecialchars($report['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <div>
                <strong>แสดงผล <?= count($reports) ?> จาก <?= $total_records ?> รายการ</strong>
            </div>

            <ul class="pagination">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&records_per_page=<?= $records_per_page ?>&search=<?= htmlspecialchars($search_query) ?>">ก่อนหน้า</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i ?>&records_per_page=<?= $records_per_page ?>&search=<?= htmlspecialchars($search_query) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&records_per_page=<?= $records_per_page ?>&search=<?= htmlspecialchars($search_query) ?>">ถัดไป</a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>