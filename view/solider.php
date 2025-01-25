<?php
// เชื่อมต่อฐานข้อมูล
require_once '../config/database.php';

// กำหนดจำนวนข้อมูลที่จะแสดงต่อหน้า
$recordsPerPage = isset($_GET['records']) ? (int) $_GET['records'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$startFrom = ($page - 1) * $recordsPerPage;

// นับจำนวนข้อมูลทั้งหมด
$totalSql = "SELECT COUNT(*) AS total FROM Soldier";
$totalResult = mysqli_query($link, $totalSql);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// คำสั่ง SQL เพื่อดึงข้อมูล
$sql = "SELECT
            s.soldier_id_card,
            s.first_name,
            s.last_name,
            r.rotation AS rotation_name,
            t.training_unit AS training_unit_name,
            s.affiliated_unit
        FROM
            Soldier s
        INNER JOIN
            Rotation r ON s.rotation_id = r.rotation_id
        INNER JOIN
            Training t ON s.training_unit_id = t.training_unit_id
        LIMIT $startFrom, $recordsPerPage";

$result = mysqli_query($link, $sql);

// ตรวจสอบผลลัพธ์
if (!$result) {
    die("Query failed: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ข้อมูลทหาร</title>

    <!-- CSS -->
    <link rel="stylesheet" <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


    <style>
        .table-responsive {
            margin: 30px 0;
        }

        .table-wrapper {
            min-width: 1000px;
            background: #fff;
            padding: 20px 25px;
            border-radius: 3px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        .table-title {
            padding-bottom: 15px;
            background: #299be4;
            color: #fff;
            padding: 16px 30px;
            margin: -20px -25px 10px;
            border-radius: 3px 3px 0 0;
        }

        .table-title h2 {
            margin: 5px 0 0;
            font-size: 24px;
        }

        .table-title .btn {
            float: right;
            font-size: 13px;
            background: #fff;
            border: none;
            min-width: 50px;
            border-radius: 2px;
            margin-left: 10px;
        }

        table.table tr th,
        table.table tr td {
            border-color: #e9e9e9;
            padding: 12px 15px;
            vertical-align: middle;
        }

        table.table td a {
            font-weight: bold;
            color: #566787;
        }

        table.table td a:hover {
            color: #2196F3;
        }

        .pagination {
            float: right;
            margin: 0 0 5px;
        }

        .pagination {
            margin: 20px 0;
            font-size: 14px;
        }

        .pagination .page-link {
            color: #007bff;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            margin: 0 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination .page-link:hover {
            background-color: rgb(9, 121, 241);
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .hint-text {
            float: left;
            margin-top: 10px;
            font-size: 13px;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include '../components/nav_bar.php'; ?>

        <!-- Sidebar -->
        <?php include '../components/sidebar.php'; ?>

        <!-- Content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <h1>ข้อมูลทหาร</h1>

                    <div class="container-xl">
                        <div class="table-responsive">
                            <div class="table-wrapper">
                                <div class="table-title">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h2>ข้อมูลทหาร</h2>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <form method="get" class="d-inline">
                                                <select name="records" class="form-control w-auto d-inline"
                                                    onchange="this.form.submit()">
                                                    <option value="10" <?= $recordsPerPage == 10 ? 'selected' : '' ?>>แสดง
                                                        10 รายการ
                                                    </option>
                                                    <option value="20" <?= $recordsPerPage == 20 ? 'selected' : '' ?>>แสดง
                                                        20 รายการ
                                                    </option>
                                                    <option value="30" <?= $recordsPerPage == 30 ? 'selected' : '' ?>>แสดง
                                                        30 รายการ
                                                    </option>
                                                    <option value="40" <?= $recordsPerPage == 40 ? 'selected' : '' ?>>แสดง
                                                        40 รายการ
                                                    </option>
                                                    <option value="50" <?= $recordsPerPage == 50 ? 'selected' : '' ?>>แสดง
                                                        50 รายการ
                                                    </option>
                                                </select>
                                                <input type="hidden" name="page" value="1">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>เลขประจำตัวประชาชน</th>
                                            <th>ชื่อ</th>
                                            <th>นามสกุล</th>
                                            <th>รุ่น</th>
                                            <th>หน่วยฝึก</th>
                                            <th>หน่วยสังกัด</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php $count = $startFrom + 1; ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?= $count++ ?></td>
                                                    <td><?= $row['soldier_id_card'] ?></td>
                                                    <td><?= $row['first_name'] ?></td>
                                                    <td><?= $row['last_name'] ?></td>
                                                    <td><?= $row['rotation_name'] ?></td>
                                                    <td><?= $row['training_unit_name'] ?></td>
                                                    <td><?= $row['affiliated_unit'] ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-view-profile"
                                                            data-id="<?= $row['soldier_id_card'] ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-delete"
                                                            data-id="<?= $row['soldier_id_card'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8">ไม่พบข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="clearfix">
                                    <div class="hint-text">แสดง <b><?= $recordsPerPage ?></b> จาก
                                        <b><?= $totalRecords ?></b> รายการ
                                    </div>
                                    <ul class="pagination justify-content-center">
                                        <!-- ปุ่ม "ก่อนหน้า" -->
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a href="?records=<?= $recordsPerPage ?>&page=<?= $page - 1 ?>"
                                                class="page-link">
                                                <i class="fas fa-angle-left"></i> ก่อนหน้า
                                            </a>
                                        </li>

                                        <!-- ตัวเลขหน้า -->
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a href="?records=<?= $recordsPerPage ?>&page=<?= $i ?>" class="page-link">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- ปุ่ม "ถัดไป" -->
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a href="?records=<?= $recordsPerPage ?>&page=<?= $page + 1 ?>"
                                                class="page-link">
                                                ถัดไป <i class="fas fa-angle-right"></i>
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                                <!-- ปุ่มดาวน์โหลด PDF -->
                                <form method="post" action="pdf.php">
                                    <button type="submit" class="btn btn-success">ดาวน์โหลด PDF</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br>


                </div>
            </section>
        </div>

        <!-- Footer -->
        <?php include '../components/footer.php'; ?>
    </div>

    <script>
        $(document).ready(function () {
            $('.btn-view-profile').on('click', function () {
                const soldierId = $(this).data('id');
                window.location.href = 'profile_soldier.php?id=' + soldierId;
            });

            $('.btn-delete').on('click', function () {
                const soldierId = $(this).data('id');
                Swal.fire({
                    title: 'คุณแน่ใจไหม?',
                    text: 'คุณต้องการลบข้อมูลทหารนี้หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'delete_soldier.php',
                            data: { id: soldierId },
                            success: function (response) {
                                if (response == 'success') {
                                    Swal.fire('ลบข้อมูลสำเร็จ!', 'ข้อมูลทหารถูกลบเรียบร้อยแล้ว.', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถลบข้อมูลทหารได้.', 'error');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>