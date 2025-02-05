<?php
require_once __DIR__ . '/../config/database.php';


// Fetch rotation and training data
$rotationResult = mysqli_query($link, "SELECT rotation_id, rotation FROM Rotation");
$trainingResult = mysqli_query($link, "SELECT training_unit_id, training_unit FROM Training");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soldier_id_card = mysqli_real_escape_string($link, $_POST['soldier_id_card']);
    $first_name = mysqli_real_escape_string($link, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($link, $_POST['last_name']);
    $rotation_id = intval($_POST['rotation_id']);
    $training_unit_id = intval($_POST['training_unit_id']);
    $affiliated_unit = !empty($_POST['affiliated_unit']) ? mysqli_real_escape_string($link, $_POST['affiliated_unit']) : NULL;
    $weight_kg = floatval($_POST['weight_kg']);
    $height_cm = intval($_POST['height_cm']);
    $medical_allergy_food_history = !empty($_POST['medical_allergy_food_history']) ? mysqli_real_escape_string($link, $_POST['medical_allergy_food_history']) : NULL;
    $underlying_diseases = !empty($_POST['underlying_diseases']) ? mysqli_real_escape_string($link, $_POST['underlying_diseases']) : NULL;
    $selection_method = mysqli_real_escape_string($link, $_POST['selection_method']);
    $service_duration = intval($_POST['service_duration']);

    $sql = "INSERT INTO Soldier
            (soldier_id_card, first_name, last_name, rotation_id, training_unit_id, affiliated_unit, weight_kg, height_cm, medical_allergy_food_history, underlying_diseases, selection_method, service_duration)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sssiiisssssi", $soldier_id_card, $first_name, $last_name, $rotation_id, $training_unit_id, $affiliated_unit, $weight_kg, $height_cm, $medical_allergy_food_history, $underlying_diseases, $selection_method, $service_duration);

    if (mysqli_stmt_execute($stmt)) {
        echo "success"; // ส่งค่า success กลับไปให้ JavaScript
    } else {
        echo "error"; // ส่งค่า error กลับไปให้ JavaScript
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>