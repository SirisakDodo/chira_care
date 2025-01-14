<?php
// เชื่อมต่อฐานข้อมูล
require_once '../config/database.php';

// คำสั่ง SQL เพื่อดึงข้อมูล
$sql = "SELECT
            s.soldier_id_card,
            s.first_name,
            s.last_name,
            r.rotation AS rotation_name,
            t.training_unit AS training_unit_name,
            s.affiliated_unit
        FROM
            Soldier s
        INNER JOIN
            Rotation r ON s.rotation_id = r.id
        INNER JOIN
            Training t ON s.training_unit_id = t.id";

// ดำเนินการคำสั่ง SQL
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ข้อมูลทหาร</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- jQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

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

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <h1>ข้อมูลทหาร</h1>

        <?php
        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (mysqli_num_rows($result) > 0) {
            // เริ่มต้นตาราง
            echo "<table class='table table-bordered'>";
            echo "<thead>
                    <tr>
                        <th>เลขประจำตัวประชาชน</th>
                        <th>ชื่อ</th>
                        <th>นามสกุล</th>
                        <th>รุ่น</th>
                        <th>หน่วยฝึก</th>
                        <th>หน่วยสังกัด</th>
                        <th>จัดการ</th>
                    </tr>
                  </thead>
                  <tbody>";

            // แสดงข้อมูลแต่ละแถว
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . $row['soldier_id_card'] . "</td>
                        <td>" . $row['first_name'] . "</td>
                        <td>" . $row['last_name'] . "</td>
                        <td>" . $row['rotation_name'] . "</td>
                        <td>" . $row['training_unit_name'] . "</td>
                        <td>" . $row['affiliated_unit'] . "</td>
                        <td>
                            <button class='btn btn-primary btn-view-profile' data-id='" . $row['soldier_id_card'] . "'>
                                <i class='fas fa-eye'></i> ดูโปรไฟล์
                            </button>
                            <button class='btn btn-danger btn-delete' data-id='" . $row['soldier_id_card'] . "'>
                                <i class='fas fa-trash'></i> ลบ
                            </button>
                        </td>
                    </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>ไม่พบข้อมูลทหาร</p>";
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        mysqli_close($link);
        ?>

        <!-- ปุ่มดาวน์โหลด PDF -->
        <form method="post" action="pdf.php">
            <button type="submit" class="btn btn-success">ดาวน์โหลด PDF</button>
        </form>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <?php
  $footerPath = '../components/footer.php';
  if (file_exists($footerPath)) {
      include($footerPath);
  } else {
      echo "ไม่พบไฟล์ Footer.";
  }
  ?>
      </div>
    </section>
  </div>



</div>

<script>
  $(document).ready(function() {
    // คลิกปุ่มดูโปรไฟล์
    $('.btn-view-profile').on('click', function() {
        var soldierId = $(this).data('id'); // รับ ID ของทหารที่ต้องการดูโปรไฟล์
        window.location.href = 'profile_soldier.php?id=' + soldierId; // ไปยังหน้าโปรไฟล์
    });

    // คลิกปุ่มลบ
    $('.btn-delete').on('click', function() {
        var soldierId = $(this).data('id'); // รับ ID ของทหารที่ต้องการลบ

        // แสดง SweetAlert2 เพื่อยืนยันการลบ
        Swal.fire({
          title: 'คุณแน่ใจไหม?',
          text: 'คุณต้องการลบข้อมูลทหารนี้หรือไม่?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'ลบ',
          cancelButtonText: 'ยกเลิก',
          customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton: 'swal2-cancel',
            icon: 'swal2-icon'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            // ส่งคำขอลบไปที่ไฟล์ delete_soldier.php
            $.ajax({
              type: 'POST',
              url: 'delete_soldier.php',
              data: { id: soldierId },
              success: function(response) {
                if (response == 'success') {
                  // แสดงข้อความยืนยันการลบและรีเฟรชหน้า
                  Swal.fire(
                    'ลบข้อมูลสำเร็จ!',
                    'ข้อมูลทหารถูกลบเรียบร้อยแล้ว.',
                    'success'
                  ).then(() => {
                    location.reload();
                  });
                } else {
                  Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถลบข้อมูลทหารได้.', 'error');
                }
              }
            });
          }
        });
    });
  });
</script>

</body>
</html>
