<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Starter</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

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
  <!-- Content -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <h1>Chiracare</h1>
      </div>
    </section>
  </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>





