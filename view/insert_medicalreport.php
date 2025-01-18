<?php
require_once '../config/database.php';  // Include database connection
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลจากเซสชัน
$user = $_SESSION['user'];
$training_unit_id = $user['training_unit_id'];
$training_unit = $user['training_unit'];

// ตรวจสอบว่าแบบฟอร์มถูกส่งมาไหม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $soldier_id = $_POST['soldier_id'];
    $symptom_description = $_POST['symptom_description'];
    $vital_signs_temperature = $_POST['vital_signs_temperature'];
    $vital_signs_blood_pressure = $_POST['vital_signs_blood_pressure'];
    $vital_signs_heart_rate = $_POST['vital_signs_heart_rate'];
    $pain_score = $_POST['pain_score'];

    // จัดการไฟล์ภาพ
    $atk_test_result = null;
    if (isset($_FILES['atk_test_result']) && $_FILES['atk_test_result']['error'] == 0) {
        $atk_test_result = file_get_contents($_FILES['atk_test_result']['tmp_name']);
    }

    $symptom_image = null;
    if (isset($_FILES['symptom_image']) && $_FILES['symptom_image']['error'] == 0) {
        $symptom_image = file_get_contents($_FILES['symptom_image']['tmp_name']);
    }

    $query = "INSERT INTO medicalreport
          (soldier_id, symptom_description, vital_signs_temperature, vital_signs_blood_pressure,
           vital_signs_heart_rate, pain_score, atk_test_result, symptom_image)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "isdsisis",
        $soldier_id,
        $symptom_description,
        $vital_signs_temperature,
        $vital_signs_blood_pressure,
        $vital_signs_heart_rate,
        $pain_score,
        $atk_test_result,
        $symptom_image
    );

    if (mysqli_stmt_execute($stmt)) {
        // Redirect to a new page showing the soldier's details and medical report
        header("Location: display_report.php?soldier_id=" . $soldier_id);
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}
?>





<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report Form</title>

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

        <?php
        $sidebarPath = '../components/nav_train.php';
        if (file_exists($sidebarPath)) {
            include($sidebarPath);
        } else {
            echo "ไม่พบไฟล์ Sidebar.";
        }
        ?>
        <!-- Sidebar -->
        <?php
        $sidebarPath = '../components/sidebartrain.php';
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
                    <div class="card card-primary mt-4">
                        <div class="card-header">
                            <h3 class="card-title">แบบฟอร์มรายงานส่วงป่วยประจำวัน</h3>
                        </div>
                        <div class="card-body">
                            <form action="insert_medicalreport.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>หน่วยฝึก:</label>
                                    <span style="font-weight: bold;"><?php echo $training_unit; ?></span>
                                    <input type="hidden" name="training_unit_id"
                                        value="<?php echo $training_unit_id; ?>">

                                    <div class="form-group">
                                        <label for="soldier_id">เลือกทหาร:</label>
                                        <select id="soldier_id" name="soldier_id" class="form-control" required>
                                            <option value="">-- กำลังโหลดทหาร --</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="symptom_description">คำอธิบายอาการ:</label>
                                        <textarea id="symptom_description" name="symptom_description"
                                            class="form-control"></textarea>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="form-group" style="flex: 1; margin-right: 15px;">
                                            <label for="vital_signs_temperature">อุณหภูมิ (°C):</label>
                                            <input type="number" step="0.1" id="vital_signs_temperature"
                                                name="vital_signs_temperature" class="form-control">
                                        </div>
                                        <div class="form-group" style="flex: 1; margin-right: 15px;">
                                            <label for="vital_signs_blood_pressure">ความดันโลหิต:</label>
                                            <input type="text" id="vital_signs_blood_pressure"
                                                name="vital_signs_blood_pressure" class="form-control">
                                        </div>
                                        <div class="form-group" style="flex: 1; margin-right: 15px;">
                                            <label for="vital_signs_heart_rate">อัตราการเต้นของหัวใจ (bpm):</label>
                                            <input type="number" id="vital_signs_heart_rate"
                                                name="vital_signs_heart_rate" class="form-control">
                                        </div>
                                        <div class="form-group" style="flex: 1;">
                                            <label for="pain_score">คะแนนความเจ็บปวด (0-10):</label>
                                            <input type="number" id="pain_score" name="pain_score" min="0" max="10"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-row" style="display: flex; gap: 20px;">
                                        <div class="form-group">
                                            <label for="atk_test_result">ผลตรวจ ATK:</label>
                                            <input type="file" id="atk_test_result" name="atk_test_result"
                                                class="form-control-file">
                                        </div>
                                        <div class="form-group">
                                            <label for="symptom_image">รูปภาพอาการ:</label>
                                            <input type="file" id="symptom_image" name="symptom_image"
                                                class="form-control-file">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                                        <a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>



    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>
        function fetchSoldiers() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_soldiers.php?training_unit_id=<?php echo $training_unit_id; ?>", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("soldier_id").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
        window.onload = fetchSoldiers;
    </script>
</body>