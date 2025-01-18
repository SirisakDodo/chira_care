<?php
require_once '../config/database.php';

// Fetch Rotation and Training options
$rotationResult = mysqli_query($link, "SELECT rotation_id, rotation FROM Rotation");
$trainingResult = mysqli_query($link, "SELECT training_unit_id, training_unit FROM Training");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_soldier'])) {
    // รับค่าจากฟอร์ม
    $soldier_id_card = mysqli_real_escape_string($link, $_POST['soldier_id_card']);
    $first_name = mysqli_real_escape_string($link, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($link, $_POST['last_name']);
    $rotation_id = intval($_POST['rotation_id']);
    $training_unit_id = intval($_POST['training_unit_id']);
    $affiliated_unit = !empty($_POST['affiliated_unit']) ? mysqli_real_escape_string($link, $_POST['affiliated_unit']) : NULL;
    $weight_kg = floatval($_POST['weight_kg']);
    $height_cm = intval($_POST['height_cm']);
    $medical_allergy_food_history = !empty($_POST['medical_allergy_food_history']) ? mysqli_real_escape_string($link, $_POST['medical_allergy_food_history']) : NULL;
    $underlying_diseases = !empty($_POST['underlying_diseases']) ? mysqli_real_escape_string($link, $_POST['underlying_diseases']) : NULL;
    $selection_method = mysqli_real_escape_string($link, $_POST['selection_method']);
    $service_duration = intval($_POST['service_duration']);

    // SQL Command
    $sql = "INSERT INTO Soldier (soldier_id_card, first_name, last_name, rotation_id, training_unit_id, affiliated_unit, weight_kg, height_cm, medical_allergy_food_history, underlying_diseases, selection_method, service_duration)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($link));
    }

    // ตรวจสอบจำนวนตัวแปรที่ส่งไปใน bind_param ให้ตรงกับจำนวน placeholders "?"
    $types = "sssiissssssi"; // ชนิดข้อมูล
    if (!mysqli_stmt_bind_param($stmt, $types, 
        $soldier_id_card, $first_name, $last_name, $rotation_id, $training_unit_id, 
        $affiliated_unit, $weight_kg, $height_cm, $medical_allergy_food_history, 
        $underlying_diseases, $selection_method, $service_duration)) {
        die("Bind failed: " . mysqli_stmt_error($stmt));
    }

    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    echo "Data inserted successfully!";
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เพิ่มข้อมูลทหาร</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        $navbarPath = '../components/nav_bar.php';
        if (file_exists($navbarPath)) {
            include($navbarPath);
        } else {
            echo "ไม่พบไฟล์ Navbar.";
        }

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

                    <body class="container py-5">
                        <h1 class="mb-4 text-center">เพิ่มข้อมูลทหาร</h1>
                        <form method="POST" action="" class="p-4 border rounded shadow-sm bg-light">
                            <div class="row form-section">
                                <div class="col-md-4">
                                    <label for="soldier_id_card">เลขประจำตัวประชาชน :</label>
                                    <input type="text" id="soldier_id_card" name="soldier_id_card" class="form-control"
                                        required maxlength="13">
                                </div>
                                <div class="col-md-4">
                                    <label for="first_name">ชื่อ:</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="last_name">นามสกุล:</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                                </div>
                            </div>

                            <div class="row form-section">
                                <div class="col-md-4">
                                    <label for="rotation_id">รุ่น:</label>
                                    <select id="rotation_id" name="rotation_id" class="form-select" required>
                                        <option value="">เลือกรุ่น</option>
                                        <?php
                                        if (mysqli_num_rows($rotationResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($rotationResult)) {
                                                echo "<option value='" . $row['rotation_id'] . "'>" . $row['rotation'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>ไม่มีข้อมูลรุ่น</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="training_unit_id">หน่วยฝึก:</label>
                                    <select id="training_unit_id" name="training_unit_id" class="form-select" required>
                                        <option value="">เลือกหน่วยฝึก</option>
                                        <?php
                                        if (mysqli_num_rows($trainingResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($trainingResult)) {
                                                echo "<option value='" . $row['training_unit_id'] . "'>" . $row['training_unit'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>ไม่มีข้อมูลหน่วยฝึก</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="affiliated_unit">หน่วยสังกัด:</label>
                                    <input type="text" id="affiliated_unit" name="affiliated_unit" class="form-control">
                                </div>
                            </div>

                            <div class="row form-section">
                                <div class="col-md-6">
                                    <label for="weight_kg">น้ำหนัก (กิโลกรัม):</label>
                                    <input type="number" step="0.1" id="weight_kg" name="weight_kg"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="height_cm">ส่วนสูง (เซนติเมตร):</label>
                                    <input type="number" id="height_cm" name="height_cm" class="form-control">
                                </div>
                            </div>

                            <div class="row form-section">
                                <div class="col-md-6">
                                    <label for="medical_allergy_food_history">ประวัติการแพ้ยา/อาหาร:</label>
                                    <textarea id="medical_allergy_food_history" name="medical_allergy_food_history"
                                        class="form-control"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="underlying_diseases">โรคประจำตัว:</label>
                                    <textarea id="underlying_diseases" name="underlying_diseases"
                                        class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="row form-section">
                                <div class="col-md-6">
                                    <label for="selection_method">วิธีการคัดเลือก:</label>
                                    <input type="text" id="selection_method" name="selection_method"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="service_duration">ระยะเวลาปฏิบัติงาน (เดือน):</label>
                                    <input type="number" id="service_duration" name="service_duration"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="add_soldier"
                                    class="btn btn-primary">เพิ่มข้อมูลทหาร</button>
                            </div>
                        </form>
                    </body>
                </div>
            </section>
        </div>

        <?php
        $footerPath = '../components/footer.php';
        if (file_exists($footerPath)) {
            include($footerPath);
        } else {
            echo "ไม่พบไฟล์ Footer.";
        }
        ?>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>