<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
        <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">ChiraCare</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Existing Menu Item -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>จัดการข้อมูลทหาร
                            <i class="right fas fa-angle-left"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="solider.php" class="nav-link">
                                <p>รายชื่อทหาร</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="addsolider.php" class="nav-link">
                                <p>เพิ่มรายชื่อทหาร</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rotation.php" class="nav-link">
                                <p>เพิ่มผลัด/หน่วย</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Additional Dashboard Items -->
                <li class="nav-item">
                    <a href="send_to_hospital.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>ส่งป่วยประจำวัน</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>

                </li>

                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>