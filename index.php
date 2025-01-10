<?php
require_once 'config.php';

// การเพิ่มข้อมูลใน Rotation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rotation'])) {
    $rotation = $_POST['rotation'];
    $sql = "INSERT INTO Rotation (rotation) VALUES ('$rotation')";
    mysqli_query($link, $sql);
}

// การเพิ่มข้อมูลใน Training
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_training'])) {
    $training_unit = $_POST['training_unit'];
    $sql = "INSERT INTO Training (training_unit) VALUES ('$training_unit')";
    mysqli_query($link, $sql);
}

// การเพิ่มข้อมูล Soldier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_soldier'])) {
    $soldier_id_card = $_POST['soldier_id_card'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $rotation_id = $_POST['rotation_id'];
    $training_unit_id = $_POST['training_unit_id'];
    $affiliated_unit = $_POST['affiliated_unit'];
    $weight_kg = $_POST['weight_kg'];
    $height_cm = $_POST['height_cm'];
    $medical_history = $_POST['medical_history'];
    $underlying_diseases = $_POST['underlying_diseases'];

    $sql = "INSERT INTO Soldier (soldier_id_card, first_name, last_name, rotation_id, training_unit_id, affiliated_unit, weight_kg, height_cm, medical_allergy_food_history, underlying_diseases)
            VALUES ('$soldier_id_card', '$first_name', '$last_name', $rotation_id, $training_unit_id, '$affiliated_unit', $weight_kg, $height_cm, '$medical_history', '$underlying_diseases')";
    mysqli_query($link, $sql);
}

// ดึงข้อมูล Rotation และ Training
$rotations = mysqli_query($link, "SELECT * FROM Rotation");
$trainings = mysqli_query($link, "SELECT * FROM Training");

// ดึงข้อมูล Soldier
$soldiers = mysqli_query($link, "SELECT s.*, r.rotation, t.training_unit
                                 FROM Soldier s
                                 JOIN Rotation r ON s.rotation_id = r.id
                                 JOIN Training t ON s.training_unit_id = t.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Military Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        h1, h2 {
            color: #333;
        }
        form {
            background: #fff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }
        form h2 {
            margin-top: 0;
        }
        form input, form select, form textarea, form button {
            display: block;
            width: calc(100% - 20px);
            margin: 10px auto;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table thead {
            background: #007bff;
            color: white;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            text-align: center;
        }
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        table tbody tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <header>
        <h1>Military</h1>
    </header>
    <main>
        <!-- Form เพิ่ม Rotation -->
        <form method="POST" action="">
            <h2>Add Rotation</h2>
            <input type="text" name="rotation" placeholder="Rotation" required>
            <button type="submit" name="add_rotation">Add Rotation</button>
        </form>

        <!-- Form เพิ่ม Training -->
        <form method="POST" action="">
            <h2>Add Training</h2>
            <input type="text" name="training_unit" placeholder="Training Unit" required>
            <button type="submit" name="add_training">Add Training</button>
        </form>

        <!-- Form เพิ่ม Soldier -->
        <form method="POST" action="">
            <h2>Add Soldier</h2>
            <input type="text" name="soldier_id_card" placeholder="ID Card" required>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <select name="rotation_id" required>
                <option value="">Select Rotation</option>
                <?php while ($row = mysqli_fetch_assoc($rotations)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['rotation']; ?></option>
                <?php endwhile; ?>
            </select>
            <select name="training_unit_id" required>
                <option value="">Select Training Unit</option>
                <?php while ($row = mysqli_fetch_assoc($trainings)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['training_unit']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="affiliated_unit" placeholder="Affiliated Unit">
            <input type="number" name="weight_kg" placeholder="Weight (kg)" step="0.1">
            <input type="number" name="height_cm" placeholder="Height (cm)">
            <textarea name="medical_history" placeholder="Medical/Allergy/Food History"></textarea>
            <textarea name="underlying_diseases" placeholder="Underlying Diseases"></textarea>
            <button type="submit" name="add_soldier">Add Soldier</button>
        </form>

        <!-- แสดงข้อมูล Soldier -->
        <h2 style="text-align: center;">Soldiers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Card</th>
                    <th>Name</th>
                    <th>Rotation</th>
                    <th>Training Unit</th>
                    <th>Affiliated Unit</th>
                    <th>Weight (kg)</th>
                    <th>Height (cm)</th>
                    <th>Medical History</th>
                    <th>Underlying Diseases</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = mysqli_fetch_assoc($soldiers)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['soldier_id_card']; ?></td>
            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
            <td><?php echo $row['rotation']; ?></td>
            <td><?php echo $row['training_unit']; ?></td>
            <td><?php echo $row['affiliated_unit']; ?></td>
            <td><?php echo $row['weight_kg']; ?></td>
            <td><?php echo $row['height_cm']; ?></td>
            <td><?php echo $row['medical_allergy_food_history']; ?></td>
            <td><?php echo $row['underlying_diseases']; ?></td>
            <>
                <a href="profile.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: white; background: #007bff; padding: 5px 10px; border-radius: 5px;">ดูโปรไฟล์</a>

    <a href="edit.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: white; background: #ffc107; padding: 5px 10px; border-radius: 5px;">Edit</a>
    <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');" style="text-decoration: none; color: white; background: #dc3545; padding: 5px 10px; border-radius: 5px;">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

        </table>
    </main>
</body>
</html>
