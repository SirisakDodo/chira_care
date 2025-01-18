<?php
require_once '../config/database.php';  // รวมไฟล์การเชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าได้เลือกกรองหรือยัง
$rotation_filter = isset($_GET['rotation']) ? $_GET['rotation'] : '';
$training_unit_filter = isset($_GET['training_unit']) ? $_GET['training_unit'] : '';

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลที่ตรงกับเงื่อนไขที่กรอง
$query = "SELECT s.soldier_id, s.first_name, s.last_name, r.rotation AS rotation_name, t.training_unit AS training_unit_name,
                 s.affiliated_unit, mr.symptom_description, mr.report_date, mr.medical_report_id,
                 mr.atk_test_result, mr.vital_signs_temperature, mr.vital_signs_blood_pressure, mr.vital_signs_heart_rate, mr.pain_score
          FROM medicalreport mr
          JOIN soldier s ON mr.soldier_id = s.soldier_id
          LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
          LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
          WHERE mr.status = 'sent'";

if ($rotation_filter) {
    $query .= " AND r.rotation_id = '$rotation_filter'";
}

if ($training_unit_filter) {
    $query .= " AND t.training_unit_id = '$training_unit_filter'";
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

// รับค่า ID ของรายงานการแพทย์ (medical_report_id) สำหรับการแสดงรูปภาพ
if (isset($_GET['id'])) {
    $medical_report_id = $_GET['id'];

    // คำสั่ง SQL สำหรับดึงข้อมูลภาพจากฐานข้อมูล
    $query = "SELECT atk_test_result FROM medicalreport WHERE medical_report_id = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "b", $medical_report_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $atk_test_result);

    // ตรวจสอบผลลัพธ์และแสดงผล
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_fetch($stmt);

        // แสดงภาพโดยใช้ base64_encode
        echo "<img src='data:image/jpeg;base64," . base64_encode($atk_test_result) . "' alt='ATK Test Result' />";
    } else {
        echo "ไม่พบข้อมูลรูปภาพ";
    }

    // ปิดการเชื่อมต่อ
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อทหารที่สถานะส่งไปแล้ว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KyZXEJ2QfJfS0PjYI6D5XzFSbF31hbb1dHLJ4mz5IKK9j8o6Hh2OxlH2wz9LkCUl" crossorigin="anonymous">
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

        .form-control,
        select.form-control {
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

        .form-control:focus {
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

<body>
    <div class="container">
        <h1>รายชื่อทหารที่สถานะส่งไปแล้ว</h1>

        <!-- Dropdown สำหรับกรองผลัดและหน่วยฝึก -->
        <div class="filter-form">
            <form method="GET">
                <select name="rotation" class="form-control" onchange="this.form.submit()">
                    <option value="">เลือกผลัด</option>
                    <!-- เติมค่าผลัดจากฐานข้อมูล -->
                    <?php
                    $rotation_query = "SELECT * FROM rotation";
                    $rotation_result = mysqli_query($link, $rotation_query);
                    while ($rotation = mysqli_fetch_assoc($rotation_result)) {
                        echo "<option value='{$rotation['rotation_id']}'" . ($rotation['rotation_id'] == $rotation_filter ? ' selected' : '') . ">{$rotation['rotation']}</option>";
                    }
                    ?>
                </select>
                <select name="training_unit" class="form-control" onchange="this.form.submit()">
                    <option value="">เลือกหน่วยฝึก</option>
                    <!-- เติมค่าหน่วยฝึกจากฐานข้อมูล -->
                    <?php
                    $training_query = "SELECT * FROM training";
                    $training_result = mysqli_query($link, $training_query);
                    while ($training = mysqli_fetch_assoc($training_result)) {
                        echo "<option value='{$training['training_unit_id']}'" . ($training['training_unit_id'] == $training_unit_filter ? ' selected' : '') . ">{$training['training_unit']}</option>";
                    }
                    ?>
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
                    <th>อาการ</th>
                    <th>วันที่รายงาน</th>
                    <th>อนุมัติ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['training_unit_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['rotation_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['affiliated_unit']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['symptom_description'])); ?></td>
                        <td><?php echo htmlspecialchars($row['report_date']); ?></td>
                        <td>
                            <form method="POST" class="form-container">
                                <input type="hidden" name="medical_report_id"
                                    value="<?php echo htmlspecialchars($row['medical_report_id']); ?>">
                                <div class="form-group">
                                    <label for="appointment_date">วันที่นัดหมาย:</label>
                                    <input type="datetime-local" name="appointment_date" id="appointment_date"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointment_location">สถานที่นัดหมาย:</label>
                                    <select name="appointment_location" id="appointment_location" class="form-control"
                                        required>
                                        <option value="OPD">OPD</option>
                                        <option value="ER">ER</option>
                                        <option value="IPD">IPD</option>
                                    </select>
                                </div>
                                <button type="submit" name="approve" class="btn-submit mt-3">บันทึกการนัดหมาย</button>
                            </form>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#detailModal<?php echo $row['medical_report_id']; ?>">
                                ดูรายละเอียด
                            </button>
                            <div class="modal fade" id="detailModal<?php echo $row['medical_report_id']; ?>" tabindex="-1"
                                aria-labelledby="detailModalLabel<?php echo $row['medical_report_id']; ?>"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="detailModalLabel<?php echo $row['medical_report_id']; ?>">
                                                รายละเอียดผู้ป่วย</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>ชื่อ-นามสกุล:</strong>
                                                <?php echo htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']); ?>
                                            </p>
                                            <p><strong>อาการ:</strong>
                                                <?php echo nl2br(htmlspecialchars($row['symptom_description'])); ?>
                                            </p>
                                            <p><strong>ผลการตรวจ ATK:</strong>
                                                <?php
                                                // ตรวจสอบให้แน่ใจว่า $atk_test_result เป็นข้อมูลที่ถูกต้องและมีขนาดที่ไม่เป็นศูนย์
                                                if (!empty($atk_test_result)) {
                                                    echo "<img src='data:image/jpeg;base64," . base64_encode($atk_test_result) . "' alt='ATK Test Result' />";
                                                } else {
                                                    echo "No test result available.";
                                                }
                                                ?>

                                            </p>
                                            <p><strong>สัญญาณชีพ:</strong><br>อุณหภูมิ:
                                                <?php echo htmlspecialchars($row['vital_signs_temperature']); ?> °C,
                                                ความดัน: <?php echo htmlspecialchars($row['vital_signs_blood_pressure']); ?>
                                                mmHg,
                                                ชีพจร: <?php echo htmlspecialchars($row['vital_signs_heart_rate']); ?> bpm
                                            </p>
                                            <p><strong>ระดับความเจ็บปวด:</strong>
                                                <?php echo htmlspecialchars($row['pain_score']); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-+0n0xW1vC5uRzZ-0g6jWoAI03fFUKDQGiR22XX5bCwAPnPp5F7fEXA5T2LsAGTI6" crossorigin="anonymous">
        </script>
</body>

</html>