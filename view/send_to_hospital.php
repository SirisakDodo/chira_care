<?php
require_once '../config/database.php';  // รวมไฟล์การเชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าได้เลือกกรองหรือยัง
$rotation_filter = isset($_GET['rotation']) ? $_GET['rotation'] : '';
$training_unit_filter = isset($_GET['training_unit']) ? $_GET['training_unit'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';  // เพิ่มตัวแปรสำหรับกรองสถานะ

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลที่ตรงกับเงื่อนไขที่กรอง
$query = "SELECT s.soldier_id, s.first_name, s.last_name, r.rotation AS rotation_name, t.training_unit AS training_unit_name,
                 s.affiliated_unit, mr.symptom_description, mr.medical_report_id
          FROM medicalreport mr
          JOIN soldier s ON mr.soldier_id = s.soldier_id
          LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
          LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
          WHERE 1";

if ($rotation_filter) {
    $query .= " AND r.rotation_id = '$rotation_filter'";
}

if ($training_unit_filter) {
    $query .= " AND t.training_unit_id = '$training_unit_filter'";
}

if ($status_filter) {
    $query .= " AND mr.status = '$status_filter'";
}

$result = mysqli_query($link, $query);

if (!$result) {
    echo "Error: " . mysqli_error($link);
    exit();
}

// การบันทึกการอนุมัติ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $medical_report_id = $_POST['medical_report_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_location = $_POST['appointment_location'];

    if (empty($appointment_date) || empty($appointment_location)) {
        echo "<script>alert('ยังไม่ได้นัดหมาย');</script>";
    } else {
        $approval_status = 'approved';
        $insert_query = "INSERT INTO medicalreportapproval (medical_report_id, approval_status, appointment_date, appointment_location)
                         VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $insert_query);
        mysqli_stmt_bind_param($stmt, "isss", $medical_report_id, $approval_status, $appointment_date, $appointment_location);

        if (mysqli_stmt_execute($stmt)) {
            $update_query = "UPDATE medicalreport SET status = 'approved' WHERE medical_report_id = ?";
            $update_stmt = mysqli_prepare($link, $update_query);
            mysqli_stmt_bind_param($update_stmt, "i", $medical_report_id);
            mysqli_stmt_execute($update_stmt);

            echo "<script>alert('นัดหมายแล้ว');</script>";
        } else {
            echo "<script>alert('การอนุมัติไม่สำเร็จ');</script>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Starter</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            padding-top: 50px;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 40px;
        }

        .table {
            margin: 0 auto;
            max-width: 1200px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px 16px;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #dee2e6;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .form-control1,
        select.form-control1 {
            height: 45px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .table td {
            font-size: 0.9rem;
        }

        .table th {
            font-size: 1rem;
        }

        .form-control1:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .container {
            padding: 20px;
        }

        .filter-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-form select {
            width: 150px;
            margin: 0 10px;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->

        <?php
        $sidebarPath = '../components/nav_bar.php';
        if (file_exists($sidebarPath)) {
            include($sidebarPath);
        } else {
            echo "ไม่พบไฟล์ Sidebar.";
        }
        ?>
        <!-- Sidebar -->
        <?php
        $sidebarPath = '../components/sidebar.php';
        if (file_exists($sidebarPath)) {
            include($sidebarPath);
        } else {
            echo "ไม่พบไฟล์ Sidebar.";
        }
        ?>
        <!-- Content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <br>
                    <h1>รายชื่อทหารที่สถานะส่งไปแล้ว</h1>

                    <!-- Dropdown สำหรับกรองผลัด, หน่วยฝึก, และสถานะ -->
                    <div class="filter-form">
                        <form method="GET">
                            <select name="rotation" class="form-control1" onchange="this.form.submit()">
                                <option value="">เลือกผลัด</option>
                                <?php
                                $rotation_query = "SELECT * FROM rotation";
                                $rotation_result = mysqli_query($link, $rotation_query);
                                while ($rotation = mysqli_fetch_assoc($rotation_result)) {
                                    echo "<option value='{$rotation['rotation_id']}'" . ($rotation['rotation_id'] == $rotation_filter ? ' selected' : '') . ">{$rotation['rotation']}</option>";
                                }
                                ?>
                            </select>
                            <select name="training_unit" class="form-control1" onchange="this.form.submit()">
                                <option value="">เลือกหน่วยฝึก</option>
                                <?php
                                $training_query = "SELECT * FROM training";
                                $training_result = mysqli_query($link, $training_query);
                                while ($training = mysqli_fetch_assoc($training_result)) {
                                    echo "<option value='{$training['training_unit_id']}'" . ($training['training_unit_id'] == $training_unit_filter ? ' selected' : '') . ">{$training['training_unit']}</option>";
                                }
                                ?>
                            </select>
                            <select name="status" class="form-control1" onchange="this.form.submit()">
                                <option value="">เลือกสถานะ</option>
                                <option value="sent" <?php echo ($status_filter == 'sent') ? 'selected' : ''; ?>>
                                    ยังไม่ได้นัดหมาย
                                </option>
                                <option value="approved" <?php echo ($status_filter == 'approved') ? 'selected' : ''; ?>>
                                    นัดหมายแล้ว
                                </option>
                            </select>
                        </form>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>หน่วยฝึก</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>ผลัด</th>
                                <th>หน่วยที่สังกัด</th>
                                <th>รายละเอียด</th>
                                <th>วันที่นัดหมาย</th>
                                <th>สถานที่นัดหมาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['training_unit_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['rotation_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['affiliated_unit']); ?></td>
                                    <td>
                                        <div class="modal-body">
                                            <p><strong>อาการ:</strong>
                                                <?php echo nl2br(htmlspecialchars($row['symptom_description'])); ?></p>
                                        </div>
                                    </td>
                                    <?php
                                    // Check if the appointment is already made
                                    $appointment_query = "SELECT appointment_date, appointment_location FROM medicalreportapproval WHERE medical_report_id = ?";
                                    $appointment_stmt = mysqli_prepare($link, $appointment_query);
                                    mysqli_stmt_bind_param($appointment_stmt, "i", $row['medical_report_id']);
                                    mysqli_stmt_execute($appointment_stmt);
                                    mysqli_stmt_store_result($appointment_stmt);
                                    mysqli_stmt_bind_result($appointment_stmt, $appointment_date, $appointment_location);

                                    if (mysqli_stmt_num_rows($appointment_stmt) > 0) {
                                        mysqli_stmt_fetch($appointment_stmt);
                                        echo "<td>" . htmlspecialchars($appointment_date) . "</td>";
                                        echo "<td>" . htmlspecialchars($appointment_location) . "</td>";
                                    } else {
                                        ?>
                                        <form method="POST" class="form-container">
                                            <td>
                                                <div class="form-group">
                                                    <input type="datetime-local" name="appointment_date" id="appointment_date"
                                                        class="form-control" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select name="appointment_location" id="appointment_location"
                                                        class="form-control" required>
                                                        <option value="OPD">OPD</option>
                                                        <option value="ER">ER</option>
                                                        <option value="IPD">IPD</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <input type="hidden" name="medical_report_id"
                                                value="<?php echo htmlspecialchars($row['medical_report_id']); ?>">
                                        </form>
                                        <?php
                                    }

                                    mysqli_stmt_close($appointment_stmt);
                                    ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" name="approve" class="btn-submit">บันทึก</button>
                    </div>
                </div>
            </section>
        </div>




    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>
