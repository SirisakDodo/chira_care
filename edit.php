<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('Invalid ID');
}

// Fetch soldier details
$soldier = mysqli_query($link, "SELECT * FROM Soldier WHERE id = $id");
$soldierData = mysqli_fetch_assoc($soldier);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $rotation_id = $_POST['rotation_id'];
    $training_unit_id = $_POST['training_unit_id'];
    $affiliated_unit = $_POST['affiliated_unit'];
    $weight_kg = $_POST['weight_kg'];
    $height_cm = $_POST['height_cm'];
    $medical_history = $_POST['medical_history'];
    $underlying_diseases = $_POST['underlying_diseases'];

    $sql = "UPDATE Soldier SET
                first_name = '$first_name',
                last_name = '$last_name',
                rotation_id = $rotation_id,
                training_unit_id = $training_unit_id,
                affiliated_unit = '$affiliated_unit',
                weight_kg = $weight_kg,
                height_cm = $height_cm,
                medical_allergy_food_history = '$medical_history',
                underlying_diseases = '$underlying_diseases'
            WHERE id = $id";

    if (mysqli_query($link, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}

// Fetch rotations and training units
$rotations = mysqli_query($link, "SELECT * FROM Rotation");
$trainings = mysqli_query($link, "SELECT * FROM Training");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Soldier</title>
</head>
<body>
    <form method="POST" action="">
        <h2>Edit Soldier</h2>
        <input type="text" name="first_name" value="<?php echo $soldierData['first_name']; ?>" required>
        <input type="text" name="last_name" value="<?php echo $soldierData['last_name']; ?>" required>
        <select name="rotation_id" required>
            <?php while ($row = mysqli_fetch_assoc($rotations)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $soldierData['rotation_id'] ? 'selected' : ''; ?>><?php echo $row['rotation']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="training_unit_id" required>
            <?php while ($row = mysqli_fetch_assoc($trainings)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $soldierData['training_unit_id'] ? 'selected' : ''; ?>><?php echo $row['training_unit']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="affiliated_unit" value="<?php echo $soldierData['affiliated_unit']; ?>">
        <input type="number" name="weight_kg" value="<?php echo $soldierData['weight_kg']; ?>">
        <input type="number" name="height_cm" value="<?php echo $soldierData['height_cm']; ?>">
        <textarea name="medical_history"><?php echo $soldierData['medical_allergy_food_history']; ?></textarea>
        <textarea name="underlying_diseases"><?php echo $soldierData['underlying_diseases']; ?></textarea>
        <button type="submit">Update</button>
    </form>
</body>
</html>
