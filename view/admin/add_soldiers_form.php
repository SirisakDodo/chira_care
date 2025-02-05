<?php
require_once __DIR__ . '/../../function/add_soldiers.php';
require_once __DIR__ . '/../../function/upload_csv.php';
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
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .upload-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 30px;
            cursor: pointer;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            background-color: #e9ecef;
        }

        .upload-area i {
            font-size: 40px;
            color: #007bff;
        }

        .hidden-input {
            display: none;
        }

        .btn-upload {
            margin-top: 20px;
            width: 100%;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
        }

        .line {
            flex: 1;
            height: 1px;
            background: darkgrey;
            border: none;

            darkgrey .divider-text {
                padding: 0 10px;
                font-weight: bold;
                color: #777;
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
            <section class="content">
                <div class="container-fluid">
                    <div class="container">
                        <div class="form-container">
                            <h3 class="mb-3">Add Soldier List</h3>
                            <p class="text-muted">Enter soldier information or upload a file</p>
                            <form id="soldierForm" action="../../function/add_soldiers.php" method="POST"
                                onsubmit="return submitForm(event)">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Surname</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">13-digit ID</label>
                                        <input type="text" class="form-control" name="soldier_id_card" required>
                                    </div>

                                    <!-- Rotation Dropdown -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rotation</label>
                                        <select class="form-select" name="rotation_id" required>
                                            <option value="">Select Rotation</option>
                                            <?php while ($row = mysqli_fetch_assoc($rotationResult)): ?>
                                                <option value="<?= $row['rotation_id']; ?>"><?= $row['rotation']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <!-- Training Unit Dropdown -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Training Unit</label>
                                        <select class="form-select" name="training_unit_id" required>
                                            <option value="">Select Training Unit</option>
                                            <?php while ($row = mysqli_fetch_assoc($trainingResult)): ?>
                                                <option value="<?= $row['training_unit_id']; ?>">
                                                    <?= $row['training_unit']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Affiliated Unit</label>
                                        <input type="text" class="form-control" name="affiliated_unit">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Weight (kg)</label>
                                        <input type="number" class="form-control" name="weight_kg">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Height (cm)</label>
                                        <input type="number" class="form-control" name="height_cm">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Chronic Disease</label>
                                        <textarea class="form-control" name="underlying_diseases"></textarea>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">History of Drug/Food Allergies</label>
                                        <textarea class="form-control" name="medical_allergy_food_history"></textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="selection_method">Selection Method</label>
                                        <input type="text" class="form-control" name="selection_method">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Period (months)</label>
                                        <input type="number" class="form-control" name="service_duration">
                                    </div>



                                    <div class="col-md-12 text-center mt-3">

                                        <button type="submit" class="btn btn-success px-4 py-2">
                                            <i class="fas fa-user-plus"></i> Add Soldier
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- เส้นคั่นและตัวแบ่ง "หรือ" -->
                            <div class="divider">
                                <hr class="line">
                                <span class="divider-text">หรือ</span>
                                <hr class="line">
                            </div>

                            <!-- ฟอร์มอัปโหลดไฟล์ CSV -->
                            <div class="upload-container">
                                <h4 class="mb-3">📂 อัปโหลดไฟล์ CSV</h4>
                                <p class="text-muted">รองรับเฉพาะไฟล์ .CSV</p>

                                <form action="../../function/upload_csv.php" method="POST"
                                    enctype="multipart/form-data">
                                    <div class="upload-area" id="uploadArea">
                                        <i class="fa fa-cloud-upload-alt"></i>
                                        <p class="text-muted" id="uploadText">ลากและวางไฟล์ที่นี่ หรือคลิกเพื่อเลือกไฟล์
                                        </p>
                                        <input type="file" name="csv_file" id="csvInput" class="hidden-input"
                                            accept=".csv" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-upload"><i
                                            class="fas fa-upload"></i>
                                        อัปโหลดไฟล์</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <br>
                </div>

            </section>
        </div>
    </div>
    <?php mysqli_close($link); ?>




    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>

    </script>
    <script src="../../assets/js/uploadcsv.js"></script>
    <script src="../../assets/js/alert.js"></script>



</body>

</html>