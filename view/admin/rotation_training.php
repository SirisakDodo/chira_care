<?php
require_once __DIR__ . '/../../function/rotation_management.php';
require_once __DIR__ . '/../../function/training_management.php';

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ตั้งค่าการอ่านภาษาไทยให้ถูกต้อง
mysqli_set_charset($link, "utf8mb4");

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

        .container {
            max-width: 900px;
            margin-top: 30px;
        }

        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 5px;
        }

        .btn-add {
            background-color: #212529;
            color: white;
            border-radius: 5px;
            padding: 6px 12px;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-add:hover {
            background-color: #343a40;
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
                    <!-- Rotating Soldiers Section -->
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold">Rotating Soldiers</h5>
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <select id="statusFilter" class="form-select" aria-label="Status Filter">
                                    <option value="all" <?= ($status_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                    <option value="active" <?= ($status_filter == 'active') ? 'selected' : ''; ?>>Active
                                    </option>
                                    <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : ''; ?>>
                                        Inactive</option>
                                </select>


                                <a href="#" class="btn-add" data-bs-toggle="modal" data-bs-target="#addRotationModal">
                                    <i class="fas fa-plus"></i> Add Rotation
                                </a>
                            </div>
                        </div>


                        <div class="card p-3">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rotation Name</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_rotation)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['rotation']); ?></td>
                                                <td>
                                                    <span
                                                        class="status-badge <?= $row['rotation_status'] == 'active' ? 'bg-success text-white' : 'bg-secondary text-white'; ?>">
                                                        <?= htmlspecialchars($row['rotation_status']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" class="text-warning"
                                                        onclick="editRotation(<?= $row['rotation_id']; ?>, '<?= htmlspecialchars($row['rotation']); ?>', '<?= $row['rotation_status']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="text-danger"
                                                        onclick="confirmDelete(<?= $row['rotation_id']; ?>, '<?= htmlspecialchars($row['rotation']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Add Rotation Modal -->
                        <div class="modal fade" id="addRotationModal" tabindex="-1"
                            aria-labelledby="addRotationModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="addRotationModalLabel">Add Rotation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="">
                                            <div class="mb-3">
                                                <label for="rotation_name" class="form-label">Rotation Name</label>
                                                <input type="text" class="form-control" id="rotation_name"
                                                    name="rotation_name" required>
                                            </div>
                                            <button type="submit" name="add_rotation" class="btn btn-dark w-100">Add
                                                Rotation</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold">Training Units</h5>
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <select id="statusFilter" class="form-select" aria-label="Status Filter">
                                    <option value="all" <?= ($status_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                    <option value="active" <?= ($status_filter == 'active') ? 'selected' : ''; ?>>Active
                                    </option>
                                    <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : ''; ?>>
                                        Inactive
                                    </option>
                                </select>

                                <a href="#" class="btn-add" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                                    <i class="fas fa-plus"></i> Add Training
                                </a>
                            </div>
                        </div>

                        <div class="card p-3">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Training Unit</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_training)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['training_unit']); ?></td>
                                                <td>
                                                    <span
                                                        class="status-badge <?= $row['training_status'] == 'active' ? 'bg-success text-white' : 'bg-secondary text-white'; ?>">
                                                        <?= htmlspecialchars($row['training_status']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" class="text-warning"
                                                        onclick="editTraining(<?= $row['training_unit_id']; ?>, '<?= htmlspecialchars($row['training_unit']); ?>', '<?= $row['training_status']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="text-danger"
                                                        onclick="confirmDelete(<?= $row['training_unit_id']; ?>, '<?= htmlspecialchars($row['training_unit']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Add Training Modal -->
                        <div class="modal fade" id="addTrainingModal" tabindex="-1"
                            aria-labelledby="addTrainingModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="addTrainingModalLabel">Add Training</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="">
                                            <div class="mb-3">
                                                <label for="training_unit" class="form-label">Training Unit</label>
                                                <input type="text" class="form-control" id="training_unit"
                                                    name="training_unit" required>
                                            </div>
                                            <button type="submit" name="add_training" class="btn btn-dark w-100">Add
                                                Training</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>



                </div>


            </section>

        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/rotation_alert.js"></script>
    <script src="../../assets/js/training_alert.js"></script>





</body>

</html>

<?php mysqli_close($link); ?>