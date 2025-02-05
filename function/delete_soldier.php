<?php
require_once __DIR__ . '/../config/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soldier_id_card = $_POST['soldier_id_card'] ?? '';

    if (!empty($soldier_id_card)) {
        $sql = "DELETE FROM soldier WHERE soldier_id_card = ?";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $soldier_id_card);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo json_encode(["status" => "success", "message" => "ลบทหารสำเร็จ!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "❌ ไม่พบข้อมูลที่ต้องการลบ"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "❌ ลบทหารไม่สำเร็จ"]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["status" => "error", "message" => "❌ SQL Error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "❌ ไม่พบ soldier_id_card"]);
    }
    mysqli_close($link);
}

?>