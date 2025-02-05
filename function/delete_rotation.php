<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug เช็คค่าที่รับมา
    file_put_contents("debug.log", print_r($data, true));

    if (isset($data['id'])) {
        $rotation_id = intval($data['id']);

        // ตรวจสอบว่ามีทหารใน rotation หรือไม่
        $check_query = "SELECT COUNT(*) AS total FROM soldier WHERE rotation_id = ?";
        $stmt = mysqli_prepare($link, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $rotation_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($check_result);

        if ($row['total'] > 0) {
            echo json_encode(["status" => "cannot_delete"]);
            mysqli_close($link);
            exit();
        }

        // ลบ Rotation
        $delete_query = "DELETE FROM rotation WHERE rotation_id = ?";
        $stmt = mysqli_prepare($link, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $rotation_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($link)]);
        }

        mysqli_close($link);
        exit();
    }
}

// ถ้าไม่มีค่า id ให้บันทึก log และส่ง error
file_put_contents("debug.log", "Invalid Request: " . print_r($data, true), FILE_APPEND);
echo json_encode(["status" => "error", "message" => "Invalid Request"]);
mysqli_close($link);
exit();
?>