<?php
require_once __DIR__ . '/../config/database.php';

// ตรวจสอบว่าเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
if (!$link) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ตั้งค่าการอ่านภาษาไทยให้ถูกต้อง
mysqli_set_charset($link, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_rotation'])) {
    $rotation_name = mysqli_real_escape_string($link, $_POST['rotation_name']);
    $query = "INSERT INTO rotation (rotation, rotation_status) VALUES ('$rotation_name', 'active')";
    mysqli_query($link, $query);
    header("Location: rotation_training.php");
    exit();
}



// Handle Change Status
if (isset($_GET['toggle_status']) && isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = intval($_GET['id']);
    $query = "UPDATE $table SET " . $table . "_status = IF(" . $table . "_status = 'active', 'inactive', 'active') WHERE " . $table . "_id = $id";
    mysqli_query($link, $query);
    header("Location: rotation_training.php");
    exit();
}

// Handle Update Rotation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_rotation'])) {
    $id = intval($_POST['id']);
    $rotation_name = mysqli_real_escape_string($link, $_POST['rotation_name']);
    $rotation_status = mysqli_real_escape_string($link, $_POST['rotation_status']);
    $query = "UPDATE rotation SET rotation = '$rotation_name', rotation_status = '$rotation_status' WHERE rotation_id = $id";
    mysqli_query($link, $query);
    echo json_encode(["success" => true, "message" => "อัปเดตผลัดสำเร็จ"]);
    exit();
}

// Handle Delete Rotation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Check if there are soldiers in this rotation
    $check_query = "SELECT COUNT(*) AS total FROM soldier WHERE rotation_id = $id";
    $check_result = mysqli_query($link, $check_query);
    $row = mysqli_fetch_assoc($check_result);

    if ($row['total'] > 0) {
        echo json_encode(["success" => false, "message" => "ไม่สามารถลบได้ เพราะมีทหารอยู่ในผลัด"]);
        exit();
    }

    $delete_query = "DELETE FROM rotation WHERE rotation_id = $id";
    $delete_result = mysqli_query($link, $delete_query);

    if ($delete_result) {
        echo json_encode(["success" => true, "message" => "ลบผลัดสำเร็จ"]);
    } else {
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการลบ"]);
    }
    exit();
}

// Fetch Data
$status_filter = 'active';
if (isset($_GET['status']) && in_array($_GET['status'], ['active', 'inactive', 'all'])) {
    $status_filter = $_GET['status'];
}

if ($status_filter == 'all') {
    $query_rotation = "SELECT * FROM rotation";
    $query_training = "SELECT * FROM training";
} else {
    $query_rotation = "SELECT * FROM rotation WHERE rotation_status = '$status_filter'";
    $query_training = "SELECT * FROM training WHERE training_status = '$status_filter'";
}

$result_rotation = mysqli_query($link, $query_rotation);

?>