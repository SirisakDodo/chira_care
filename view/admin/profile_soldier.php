<?php
require_once __DIR__ . '/../../config/database.php';

// ตรวจสอบว่า soldier_id ถูกส่งมาหรือไม่
if (!isset($_GET['soldier_id']) || empty($_GET['soldier_id'])) {
    die("ไม่พบข้อมูลทหาร กรุณาระบุ soldier_id");
}

$soldier_id = intval($_GET['soldier_id']); // ป้องกัน SQL Injection

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ตั้งค่าภาษาไทยให้ถูกต้อง
mysqli_set_charset($link, "utf8mb4");

// ดึงข้อมูลทหารจากฐานข้อมูล
$query = "
    SELECT
        s.soldier_id,
        s.soldier_id_card,
        s.first_name,
        s.last_name,
        COALESCE(r.rotation, 'N/A') AS rotation_name,
        COALESCE(t.training_unit, 'N/A') AS training_unit_name,
        COALESCE(s.affiliated_unit, 'N/A') AS affiliated_unit,
        COALESCE(s.weight_kg, 'N/A') AS weight_kg,
        COALESCE(s.height_cm, 0) AS height_cm,
        COALESCE(s.medical_allergy_food_history, 'ไม่มีข้อมูล') AS allergy,
        COALESCE(s.underlying_diseases, 'ไม่มีข้อมูล') AS diseases,
        COALESCE(s.selection_method, 'ไม่ระบุ') AS selection_method,
        COALESCE(s.service_duration, 'N/A') AS service_duration,
        s.soldier_image
    FROM soldier s
    LEFT JOIN rotation r ON s.rotation_id = r.rotation_id
    LEFT JOIN training t ON s.training_unit_id = t.training_unit_id
    WHERE s.soldier_id = $soldier_id
";

$result = mysqli_query($link, $query);

// ตรวจสอบว่าพบข้อมูลหรือไม่
if (!$result || mysqli_num_rows($result) == 0) {
    die("ไม่พบข้อมูลของทหาร ID: " . htmlspecialchars($soldier_id));
}

$soldier = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chiracare Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+Thai:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .profile-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }



        .profile-pic {
            border-radius: 10px;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #4c562c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            background: #f8f9fa;
            color: #6c757d;
        }

        .tab-header {
            border-bottom: 2px solid #4c562c;
        }

        .completed-status {
            background-color: #d4f4d2;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .text-military-green {
            color: rgb(15, 126, 0);
            /* Military Green */
            font-weight: bold;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include __DIR__ . '/../../components/nav_bar.php'; ?>
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <!-- Content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <br>
                    <div class="container">
                        <div class="profile-card p-5">
                            <div class="d-flex justify-content-between align-items-start">
                                <!-- Left Section: Profile Details -->
                                <div>
                                    <div class="mb-3">
                                        <h3 class="fw-bold text-military-green"> <!-- ใช้สีเขียวทหาร -->
                                            พลฯ
                                            <?= htmlspecialchars($soldier['first_name'] . " " . $soldier['last_name']) ?>
                                        </h3>
                                    </div>

                                    <div class="d-flex flex-wrap mb-3 fs-5"> <!-- เพิ่ม fs-5 -->
                                        <p class="mb-0 me-4"><strong>เลขบัตรประจำตัว:</strong>
                                            <?= htmlspecialchars($soldier['soldier_id_card']) ?></p>
                                        <p class="me-4 mb-0">
                                            <strong class="text-semi-bold">การคัดเลือก:</strong>
                                            <?= htmlspecialchars($soldier['selection_method']) ?>
                                        </p>


                                        <p class="me-4 mb-0"><strong>ผลัด:</strong>
                                            <?= htmlspecialchars($soldier['rotation_name']) ?></p>
                                    </div>
                                    <!-- Training Details -->
                                    <div class="d-flex flex-wrap mb-3 fs-5">
                                        <p class="me-4 mb-0"><strong>หน่วยฝึก:</strong>
                                            <?= htmlspecialchars($soldier['training_unit_name']) ?></p>
                                        <p class="me-4 mb-0"><strong>หน่วยต้นสังกัด:</strong>
                                            <?= htmlspecialchars($soldier['affiliated_unit']) ?></p>
                                        <p class="mb-0"><strong>ระยะเวลารับราชการ:</strong>
                                            <?= htmlspecialchars($soldier['service_duration']) ?> เดือน</p>
                                    </div>

                                    <!-- Chronic Diseases & Allergies -->
                                    <div class="d-flex flex-wrap mb-3 fs-5">
                                        <p class="me-4 mb-0"><strong>โรคประจำตัว:</strong>
                                            <?= htmlspecialchars($soldier['diseases']) ?></p>
                                        <p class="mb-0"><strong>ประวัติแพ้ยา/อาหาร:</strong>
                                            <?= htmlspecialchars($soldier['allergy']) ?></p>
                                    </div>

                                    <!-- Physical Measurements -->
                                    <div class="d-flex mb-3 fs-5">
                                        <div class="me-4"><strong>น้ำหนัก:</strong>
                                            <?= htmlspecialchars($soldier['weight_kg']) ?> kg</div>
                                        <div class="me-4"><strong>ส่วนสูง:</strong>
                                            <?= htmlspecialchars($soldier['height_cm']) ?> cm</div>
                                        <div><strong>BMI:</strong>
                                            <?= number_format(($soldier['weight_kg'] / (($soldier['height_cm'] / 100) ** 2)), 1) ?>
                                        </div>
                                    </div>
                                </div>


                                <!-- Profile Picture -->
                                <!-- Profile Picture -->
                                <div class="text-end">
                                    <?php if (!empty($soldier['soldier_image'])): ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($soldier['soldier_image']) ?>"
                                            class="profile-pic mb-4" alt="Profile Picture"> <!-- เพิ่ม mb-3 -->
                                    <?php else: ?>
                                        <div
                                            class="profile-pic d-flex align-items-center justify-content-center bg-light text-muted mb-3">
                                            <span>ยังไม่ได้อัพโหลดรูป</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- ปุ่มแก้ไขข้อมูล -->
                                    <a href="edit_soldier.php?soldier_id=<?= urlencode($soldier['soldier_id']); ?>"
                                        class="btn btn-warning mt-2">
                                        <i class="fas fa-edit"></i> แก้ไขข้อมูล
                                    </a>
                                </div>


                            </div>
                        </div>




                        <div class="profile-card mt-4">
                            <ul class="nav nav-tabs tab-header">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Treatment History</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-muted" href="#">Mental Health Assessment</a>
                                </li>
                            </ul>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center border p-3 rounded mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0">Routine Check-up</h6>
                                        <small>Dr. Sarah Johnson</small>
                                    </div>
                                    <div>
                                        <span class="completed-status">Completed</span>
                                        <small class="text-muted d-block">15 Mar 2025</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border p-3 rounded">
                                    <div>
                                        <h6 class="fw-bold mb-0">Physical Fitness Assessment</h6>
                                        <small>Sgt. Michael Brown</small>
                                    </div>
                                    <div>
                                        <span class="completed-status">Completed</span>
                                        <small class="text-muted d-block">28 Feb 2025</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
    <?php mysqli_close($link); ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>