<?php
echo "";
flush();
require_once __DIR__ . '/../../function/fetch_soldiers.php';
require_once __DIR__ . '/../../function/delete_soldier.php';
echo "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chiracare Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+Thai:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .custom-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            background: #fff;
            position: relative;
            text-align: left;
        }

        .custom-card h5 {
            margin-bottom: 5px;
        }

        .custom-card h3 {
            font-weight: bold;
        }

        .custom-card-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            padding: 5px;
            border-radius: 50%;
        }

        .custom-card-icon i {
            font-size: 20px;
        }

        .filter-container {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            table-layout: fixed;
            /* Ensures equal width columns */
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            width: 16.66%;
            /* Distributes columns evenly */
            word-wrap: break-word;
        }

        .action-buttons i {
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
        }

        .card-header {
            margin: 10px;
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card shadow-sm custom-card">
                                <h5>Total Soldiers</h5>
                                <h3>
                                    <?php echo $total_soldiers; ?> <span
                                        style='font-size: 16px; font-weight: normal;'>people</span>
                                </h3>

                                <div class="custom-card-icon">
                                    <i class="fas fa-users" style="color: #3b82f6;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm custom-card">
                                <h5>Total Rotations</h5>
                                <h3>
                                    <?php echo $total_rotations; ?> <span
                                        style='font-size: 16px; font-weight: normal;'>rotation</span>
                                </h3>

                                <div class="custom-card-icon">
                                    <i class="fas fa-sync-alt" style="color: #f59e0b;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm custom-card">
                                <h5>Total Training Sessions</h5>
                                <h3>
                                    <?php echo $total_trainings; ?> <span
                                        style='font-size: 16px; font-weight: normal;'>training</span>
                                </h3>

                                <div class="custom-card-icon">
                                    <i class="fas fa-chalkboard-teacher" style="color: #10b981;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm custom-card">
                                <h5>Total Training Sessions</h5>
                                <h3>
                                    <?php echo $total_trainings; ?> <span
                                        style='font-size: 16px; font-weight: normal;'>training</span>
                                </h3>

                                <div class="custom-card-icon">
                                    <i class="fas fa-chalkboard-teacher" style="color: #10b981;"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="filter-container d-flex justify-content-between align-items-center">
                        <div>
                            <select class="form-control d-inline-block w-auto">
                                <option>All Shifts</option>
                            </select>
                            <select class="form-control d-inline-block w-auto">
                                <option>All Units</option>
                            </select>
                        </div>
                        <div>
                            <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Soldier</button>
                            <button class="btn btn-success"><i class="fas fa-plus"></i> Add Shift</button>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h2>Soldiers List</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>ID Number</th>
                                            <th>Shift</th>
                                            <th>Training Unit</th>
                                            <th>Parent Unit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($row['soldier_id_card']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($row['rotation_name'] ?? 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($row['training_unit_name'] ?? 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($row['affiliated_unit']); ?>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="profile_soldier.php?soldier_id=<?= urlencode($row['soldier_id']); ?>"
                                                        title="View">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </a>
                                                    <a href="edit_soldier.php?soldier_id=<?= urlencode($row['soldier_id']); ?>"
                                                        title="View">
                                                        <i class="fas fa-edit text-success"></i>
                                                    </a>
                                                    <i class="fas fa-trash text-danger delete-soldier" title="Delete"
                                                        data-id="<?php echo htmlspecialchars($row['soldier_id_card']); ?>"></i>

                                                </td>

                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
    <script src="../../assets/js/alertdelete.js"></script>


</body>

</html>