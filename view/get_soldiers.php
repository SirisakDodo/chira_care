<?php
require_once '../config/database.php';  // Include database connection

$training_unit_id = intval($_GET['training_unit_id']);
$soldiers_query = "SELECT soldier_id, CONCAT(first_name, ' ', last_name) AS full_name FROM soldier WHERE training_unit_id = $training_unit_id";
$soldiers_result = mysqli_query($link, $soldiers_query);

echo '<option value="">-- เลือกทหาร --</option>';
while ($row = mysqli_fetch_assoc($soldiers_result)) {
    echo '<option value="' . $row['soldier_id'] . '">' . $row['full_name'] . '</option>';
}
?>
