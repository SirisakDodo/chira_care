<?php
require_once __DIR__ . '/../../config/database.php';

// ตรวจสอบ soldier_id
if (!isset($_GET['soldier_id']) || empty($_GET['soldier_id'])) {
    die("ไม่พบข้อมูลทหาร กรุณาระบุ soldier_id");
}

$soldier_id = intval($_GET['soldier_id']);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8mb4");

// ดึงข้อมูลทหารจากฐานข้อมูล
$query = "
    SELECT s.*,
           r.rotation_id, r.rotation AS rotation_name,
           t.training_unit_id, t.training_unit AS training_unit_name
    FROM soldier s
    LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
    LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
    WHERE s.soldier_id = $soldier_id
";
$result = mysqli_query($link, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("ไม่พบข้อมูลของทหาร ID: " . htmlspecialchars($soldier_id));
}

$soldier = mysqli_fetch_assoc($result);

// ดึงข้อมูลหมวดหมู่ `<select>`
$rotation_query = mysqli_query($link, "SELECT * FROM rotation");
$training_query = mysqli_query($link, "SELECT * FROM training");

// ตรวจจับการกดปุ่ม "บันทึก"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = mysqli_real_escape_string($link, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($link, $_POST['last_name']);
    $rotationId = intval($_POST['rotation_id']);
    $trainingUnitId = intval($_POST['training_unit_id']);
    $affiliatedUnit = mysqli_real_escape_string($link, $_POST['affiliated_unit']);
    $weightKg = floatval($_POST['weight_kg']);
    $heightCm = intval($_POST['height_cm']);
    $medicalAllergyFoodHistory = mysqli_real_escape_string($link, $_POST['medical_allergy_food_history']);
    $underlyingDiseases = mysqli_real_escape_string($link, $_POST['underlying_diseases']);
    $selectionMethod = mysqli_real_escape_string($link, $_POST['selection_method']);
    $serviceDuration = intval($_POST['service_duration']);

    // รับข้อมูลภาพที่อัปโหลด
    $soldierImage = null;
    if (isset($_FILES['soldier_image']) && $_FILES['soldier_image']['error'] == 0) {
        $soldierImage = file_get_contents($_FILES['soldier_image']['tmp_name']);
    }

    // อัปเดตข้อมูล
    $update_query = "
        UPDATE soldier SET
            first_name = '$firstName',
            last_name = '$lastName',
            rotation_id = '$rotationId',
            training_unit_id = '$trainingUnitId',
            affiliated_unit = '$affiliatedUnit',
            weight_kg = '$weightKg',
            height_cm = '$heightCm',
            medical_allergy_food_history = '$medicalAllergyFoodHistory',
            underlying_diseases = '$underlyingDiseases',
            selection_method = '$selectionMethod',
            service_duration = '$serviceDuration'
    ";

    if ($soldierImage) {
        $soldierImage = mysqli_real_escape_string($link, $soldierImage);
        $update_query .= ", soldier_image = '$soldierImage'";
    }

    $update_query .= " WHERE soldier_id = $soldier_id";

    if (mysqli_query($link, $update_query)) {
        echo "<script>alert('อัปเดตข้อมูลเรียบร้อย!'); window.location='profile_soldier.php?soldier_id=$soldier_id';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Soldiers</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        .card {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-control,
        .form-select {
            height: 45px;
            /* ให้ input box เท่ากัน */
            font-size: 16px;
        }

        .upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px dashed #6c757d;
            text-align: center;
            width: 100%;
        }

        .upload-label:hover {
            background: #e9ecef;
        }


        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #6c757d;
            margin-top: 10px;
        }

        .form-label {
            font-weight: bold;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar & Sidebar -->
        <?php include __DIR__ . '/../../components/nav_bar.php'; ?>
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <!-- Content -->

        <div class="content-wrapper">
            <br>
            <section class="content">
                <div class="container-fluid">
                    <div class="container">
                        <div class="card p-4">
                            <h2 class="mb-4 text-center text-primary"><i class="fas fa-user-edit"></i> Edit Soldier
                                Information</h2>

                            <form method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="first_name" class="form-control"
                                                    value="<?= htmlspecialchars($soldier['first_name']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Surname</label>
                                                <input type="text" name="last_name" class="form-control"
                                                    value="<?= htmlspecialchars($soldier['last_name']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">13-digit ID</label>
                                                <input type="text" name="soldier_id_card" class="form-control"
                                                    value="<?= htmlspecialchars($soldier['soldier_id_card']) ?>"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rotation</label>
                                                <select name="rotation_id" class="form-control">
                                                    <?php while ($row = mysqli_fetch_assoc($rotation_query)): ?>
                                                        <option value="<?= $row['rotation_id'] ?>"
                                                            <?= ($row['rotation_id'] == $soldier['rotation_id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($row['rotation']) ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Training Unit</label>
                                                    <select name="training_unit_id" class="form-control">
                                                        <?php while ($row = mysqli_fetch_assoc($training_query)): ?>
                                                            <option value="<?= $row['training_unit_id'] ?>"
                                                                <?= ($row['training_unit_id'] == $soldier['training_unit_id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($row['training_unit']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Affiliated Unit</label>
                                                    <input type="text" name="affiliated_unit" class="form-control"
                                                        value="<?= htmlspecialchars($soldier['affiliated_unit']) ?>"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Weight (kg)</label>
                                                    <input type="number" step="0.1" name="weight_kg"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($soldier['weight_kg']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Height (cm)</label>
                                                    <input type="number" name="height_cm" class="form-control"
                                                        value="<?= htmlspecialchars($soldier['height_cm']) ?>" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Chronic Disease</label>
                                                <input type="text" name="underlying_diseases" class="form-control"
                                                    value="<?= htmlspecialchars($soldier['underlying_diseases']) ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">History of Drug/Food Allergies</label>
                                                <input type="text" name="medical_allergy_food_history"
                                                    class="form-control"
                                                    value="<?= htmlspecialchars($soldier['medical_allergy_food_history']) ?>">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Selection Method</label>
                                                    <input type="text" name="selection_method" class="form-control"
                                                        value="<?= htmlspecialchars($soldier['selection_method']) ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Service Duration (months)</label>
                                                    <input type="number" name="service_duration" class="form-control"
                                                        value="<?= htmlspecialchars($soldier['service_duration']) ?>"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Profile Picture Upload (Moved Below Selection Method) -->
                                            <div class="col-md-6 mb-3">
                                                <h5 class="text-center">Profile_soldier</h5>
                                                <!-- เปลี่ยนชื่อให้เป็นภาษาไทย -->

                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="text-center">
                                                        <?php if (!empty($soldier['soldier_image'])): ?>
                                                            <img id="imagePreview" class="profile-pic mt-2"
                                                                src="data:image/jpeg;base64,<?= base64_encode($soldier['soldier_image']) ?>"
                                                                alt="Profile Picture">
                                                        <?php else: ?>
                                                            <div id="noImageText"
                                                                class="text-muted border rounded d-flex align-items-center justify-content-center"
                                                                style="width: 150px; height: 150px; border: 2px dashed #6c757d; font-size: 14px;">
                                                                ยังไม่ได้แนบรูป
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <label class="upload-label mt-2 p-2" for="soldier_image"
                                                        style="width: 180px; padding: 8px; border-radius: 10px;">
                                                        <i class="fas fa-upload fa-lg text-muted"></i>
                                                        <p class="mb-0 text-muted" style="font-size: 14px;">อัปโหลดรูป
                                                        </p>
                                                        <input type="file" name="soldier_image" id="soldier_image"
                                                            class="d-none" onchange="previewImage(event)">
                                                    </label>
                                                </div>
                                            </div>


                                            <div class="mt-4 text-center">
                                                <button type="submit" class="btn btn-success"><i
                                                        class="fas fa-save"></i>
                                                    Save</button>
                                                <a href="profile_soldier.php?soldier_id=<?= urlencode($soldier_id) ?>"
                                                    class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>

                    </div>

                    <br>
                </div>

            </section>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('imagePreview');
                const noImageText = document.getElementById('noImageText');

                if (output) {
                    output.src = reader.result;
                    output.style.display = "block";
                }

                if (noImageText) {
                    noImageText.style.display = "none";
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>



</body>

</html>