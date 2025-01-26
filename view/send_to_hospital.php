<?php
require_once '../config/database.php';  // รวมไฟล์การเชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่าได้เลือกกรองหรือยัง
$rotation_filter = isset($_GET['rotation']) ? $_GET['rotation'] : '';
$training_unit_filter = isset($_GET['training_unit']) ? $_GET['training_unit'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'sent';  // กำหนดค่าเริ่มต้นเป็น 'sent'
// เพิ่มตัวแปรสำหรับกรองสถานะ
$query = "SELECT s.soldier_id, s.first_name, s.last_name,
                 r.rotation AS rotation_name,
                 t.training_unit AS training_unit_name,
                 s.affiliated_unit,
                 mr.symptom_description,
                 mr.report_date,
                 mr.medical_report_id,
                 mr.atk_test_result,
                 mr.vital_signs_temperature,
                 mr.vital_signs_blood_pressure,
                 mr.vital_signs_heart_rate,
                 mr.pain_score,
                 mr.status,
                 ma.appointment_date,
                 ma.appointment_location
         FROM medicalreport mr
         JOIN soldier s ON mr.soldier_id = s.soldier_id
         LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
         LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
         LEFT JOIN medicalreportapproval ma ON mr.medical_report_id = ma.medical_report_id
         WHERE mr.status IN ('sent', 'approved')";

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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $medical_report_id = $_POST['medical_report_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_location = $_POST['appointment_location'];

    // ตรวจสอบสถานะของ medical_report ก่อนอัปเดตข้อมูล
    $check_status_query = "SELECT status FROM medicalreport WHERE medical_report_id = ?";
    $stmt_check = mysqli_prepare($link, $check_status_query);
    mysqli_stmt_bind_param($stmt_check, "i", $medical_report_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $current_status);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($current_status === 'approved') {
        echo "<script>alert('ข้อมูลนี้ได้รับการอนุมัติแล้ว ไม่สามารถบันทึกซ้ำได้');</script>";
    } else {
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
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medical Report Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
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


        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <br>
                    <h1 class="text-center">รายชื่อทหารเจ็บป่วยประจำวัน</h1>

                    <div class="filter-form text-center mb-4">
                        <form method="GET">
                            <!-- เลือกผลัด -->
                            <select name="rotation" class="form-control d-inline-block w-auto"
                                onchange="this.form.submit()">
                                <option value="">เลือกผลัก</option>
                                <?php
                                $rotation_query = "SELECT * FROM rotation";
                                $rotation_result = mysqli_query($link, $rotation_query);
                                while ($rotation = mysqli_fetch_assoc($rotation_result)) {
                                    echo "<option value='{$rotation['rotation_id']}'" . ($rotation['rotation_id'] == $rotation_filter ? ' selected' : '') . ">{$rotation['rotation']}</option>";
                                }
                                ?>
                            </select>

                            <!-- เลือกหน่วยฝึก -->
                            <select name="training_unit" class="form-control d-inline-block w-auto"
                                onchange="this.form.submit()">
                                <option value="">เลือกหน่วยฝึก</option>
                                <?php
                                $training_query = "SELECT * FROM training";
                                $training_result = mysqli_query($link, $training_query);
                                while ($training = mysqli_fetch_assoc($training_result)) {
                                    echo "<option value='{$training['training_unit_id']}'" . ($training['training_unit_id'] == $training_unit_filter ? ' selected' : '') . ">{$training['training_unit']}</option>";
                                }
                                ?>
                            </select>

                            <!-- เลือกสถานะ -->
                            <select name="status" class="form-control d-inline-block w-auto"
                                onchange="this.form.submit()">
                                <option value="">เลือกสถานะ</option>
                                <option value="sent" <?php echo ($status_filter == 'sent') ? 'selected' : ''; ?>>
                                    ยังไม่ได้นัดหมาย</option>
                                <option value="approved" <?php echo ($status_filter == 'approved') ? 'selected' : ''; ?>>
                                    นัดหมายแล้ว</option>
                            </select>
                        </form>
                    </div>


                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ชื่อ - นามสกุล</th>
                                <th>ผลัด</th>
                                <th>หน่วยที่สังกัด</th>
                                <th>รายละเอียด</th>
                                <th>สถานะการนัดหมาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['first_name'] ?? '', ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($row['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['rotation_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['affiliated_unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <p><?php echo nl2br(htmlspecialchars($row['symptom_description'])); ?>


                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#modal<?php echo $row['medical_report_id']; ?>">
                                                ดูเพิ่มเติม
                                            </button>
                                        </p>

                                        <div class="modal fade" id="modal<?php echo $row['medical_report_id']; ?>"
                                            tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">อาการ</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">


                                                        <?php
                                                        $medical_report_id = $row['medical_report_id'];
                                                        $report_query = "SELECT symptom_image, atk_test_result FROM medicalreport WHERE medical_report_id = ?";
                                                        $stmt = mysqli_prepare($link, $report_query);
                                                        mysqli_stmt_bind_param($stmt, "i", $medical_report_id);
                                                        mysqli_stmt_execute($stmt);
                                                        mysqli_stmt_store_result($stmt);
                                                        mysqli_stmt_bind_result($stmt, $symptom_image, $atk_test_result);
                                                        mysqli_stmt_fetch($stmt);

                                                        // Display Symptom Image
                                                        if (!empty($symptom_image)) {
                                                            $symptom_image_data = base64_encode($symptom_image);
                                                            echo "<p><strong>รูปภาพอาการ:</strong></p>";
                                                            echo "<img src='data:image/jpeg;base64," . $symptom_image_data . "' class='img-fluid' alt='รูปอาการ'>";
                                                        } else {
                                                            echo "<p><strong>รูปภาพอาการ:</strong> ไม่มีข้อมูล</p>";
                                                        }

                                                        // Display ATK Test Result Image
                                                        if (!empty($atk_test_result)) {
                                                            $atk_image_data = base64_encode($atk_test_result);
                                                            echo "<p><strong>ผลตรวจ ATK:</strong></p>";
                                                            echo "<img src='data:image/jpeg;base64," . $atk_image_data . "' class='img-fluid' alt='ผลตรวจ ATK'>";
                                                        } else {
                                                            echo "<p><strong>ผลตรวจ ATK:</strong> ไม่มีข้อมูล</p>";
                                                        }

                                                        mysqli_stmt_close($stmt);
                                                        ?>



                                                        <p><strong>อาการ:</strong>
                                                            <?php echo nl2br(htmlspecialchars($row['symptom_description'])); ?>
                                                        </p>
                                                        <p><strong>สัญญาณชีพ:</strong> อุณหภูมิ:
                                                            <?php echo htmlspecialchars($row['vital_signs_temperature']); ?>
                                                            °C,
                                                            ความดันโลหิต:
                                                            <?php echo htmlspecialchars($row['vital_signs_blood_pressure']); ?>
                                                            mmHg,
                                                            ชีพจร:
                                                            <?php echo htmlspecialchars($row['vital_signs_heart_rate']); ?>
                                                            bpm
                                                        </p>
                                                        <p><strong>ระดับความเจ็บปวด:</strong>
                                                            <?php echo htmlspecialchars($row['pain_score']); ?>
                                                        </p>
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
                                                            echo "<strong>วันที่นัดหมาย:</strong> " . htmlspecialchars($appointment_date) . "<br>";
                                                            echo "<strong>สถานที่นัดหมาย:</strong> " . htmlspecialchars($appointment_location);
                                                        } else {
                                                            ?>
                                                            <form method="POST" class="form-container">
                                                                <input type="hidden" name="medical_report_id"
                                                                    value="<?php echo htmlspecialchars($row['medical_report_id']); ?>">
                                                                <div class="form-group">
                                                                    <label for="appointment_date">วันที่นัดหมาย:</label>
                                                                    <input type="datetime-local" name="appointment_date"
                                                                        id="appointment_date" class="form-control" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="appointment_location">สถานที่นัดหมาย:</label>
                                                                    <select name="appointment_location"
                                                                        id="appointment_location" class="form-control" required>
                                                                        <option value="OPD">OPD</option>
                                                                        <option value="ER">ER</option>
                                                                        <option value="IPD">IPD</option>
                                                                    </select>
                                                                </div>
                                                                <button type="submit" name="approve"
                                                                    class="btn-submit mt-3">บันทึกการนัดหมาย</button>
                                                            </form>
                                                            <?php
                                                        }

                                                        mysqli_stmt_close($appointment_stmt);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                    <td>
                                        <?php if (empty($row['status'])): ?>
                                            <span class="badge badge-secondary">ไม่มีข้อมูล</span>
                                        <?php elseif ($row['status'] === 'sent'): ?>
                                            <span class="badge badge-warning">ยังไม่ได้นัดหมาย</span>
                                            <br>วัน: <span class="text-danger">ยังไม่ระบุ</span>
                                            <br>สถานที่: <span class="text-danger">ยังไม่ระบุ</span>
                                        <?php elseif ($row['status'] === 'approved'): ?>
                                            <span class="badge badge-success">นัดหมายแล้ว</span>
                                            <br>วันที่: <?php echo htmlspecialchars($row['appointment_date']); ?>
                                            <br>สถานที่: <?php echo htmlspecialchars($row['appointment_location']); ?>
                                        <?php endif; ?>
                                    </td>



                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
        </div>
        </section>
        </>

        <!-- Footer -->
        <?php
        $sidebarPath = '../components/footer.php';
        if (file_exists($sidebarPath)) {
            include($sidebarPath);
        } else {
            echo "ไม่พบไฟล์ Sidebar.";
        }
        ?>

    </div>

    <!-- Scripts -->
    <script>
        $(document).ready(function () {
            $('.appointment-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: form.serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('เกิดข้อผิดพลาด กรุณาลองใหม่');
                    }
                });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>