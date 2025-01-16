<?php
require_once '../config/database.php';  // Include database connection

// Fetch training units
$training_query = "SELECT training_unit_id, training_unit FROM training";
$training_result = mysqli_query($link, $training_query);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $soldier_id = $_POST['soldier_id'];
    $symptom_description = $_POST['symptom_description'];
    $vital_signs_temperature = $_POST['vital_signs_temperature'];
    $vital_signs_blood_pressure = $_POST['vital_signs_blood_pressure'];
    $vital_signs_heart_rate = $_POST['vital_signs_heart_rate'];
    $pain_score = $_POST['pain_score'];

    // จัดการไฟล์ภาพ
    $atk_test_result = null;
    if (isset($_FILES['atk_test_result']['tmp_name'])) {
        $atk_test_result = file_get_contents($_FILES['atk_test_result']['tmp_name']);
    }

    $symptom_image = null;
    if (isset($_FILES['symptom_image']['tmp_name'])) {
        $symptom_image = file_get_contents($_FILES['symptom_image']['tmp_name']);
    }

    $query = "INSERT INTO medicalreport
              (soldier_id, symptom_description, vital_signs_temperature, vital_signs_blood_pressure,
               vital_signs_heart_rate, pain_score, atk_test_result, symptom_image, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "isdsisiis", $soldier_id, $symptom_description, $vital_signs_temperature,
                          $vital_signs_blood_pressure, $vital_signs_heart_rate, $pain_score,
                          $atk_test_result, $symptom_image, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo "บันทึกข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}
?>
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report Form</title>
    <script>
        function fetchSoldiers() {
            var trainingUnitId = document.getElementById("training_unit").value;
            if (trainingUnitId) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_soldiers.php?training_unit_id=" + trainingUnitId, true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById("soldier_id").innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                document.getElementById("soldier_id").innerHTML = '<option value="">Select training unit first</option>';
            }
        }
    </script>
</head>
<body>
    <h1>บันทึกรายงานทางการแพทย์</h1>
    <form action="insert_medicalreport.php" method="post" enctype="multipart/form-data">
        <label for="training_unit">หน่วยฝึก:</label><br>
        <select id="training_unit" name="training_unit" onchange="fetchSoldiers()" required>
            <option value="">-- เลือกหน่วยฝึก --</option>
            <?php while ($row = mysqli_fetch_assoc($training_result)) { ?>
                <option value="<?php echo $row['training_unit_id']; ?>"><?php echo $row['training_unit']; ?></option>
            <?php } ?>
        </select><br><br>

        <label for="soldier_id">เลือกทหาร:</label><br>
        <select id="soldier_id" name="soldier_id" required>
            <option value="">-- กรุณาเลือกหน่วยฝึกก่อน --</option>
        </select><br><br>

        <label for="symptom_description">คำอธิบายอาการ:</label><br>
        <textarea id="symptom_description" name="symptom_description"></textarea><br><br>

        <label for="vital_signs_temperature">อุณหภูมิ (°C):</label><br>
        <input type="number" step="0.1" id="vital_signs_temperature" name="vital_signs_temperature"><br><br>

        <label for="vital_signs_blood_pressure">ความดันโลหิต:</label><br>
        <input type="text" id="vital_signs_blood_pressure" name="vital_signs_blood_pressure"><br><br>

        <label for="vital_signs_heart_rate">อัตราการเต้นของหัวใจ (bpm):</label><br>
        <input type="number" id="vital_signs_heart_rate" name="vital_signs_heart_rate"><br><br>

        <label for="pain_score">คะแนนความเจ็บปวด (0-10):</label><br>
        <input type="number" id="pain_score" name="pain_score" min="0" max="10"><br><br>

        <label for="atk_test_result">ผลการทดสอบ X-ray:</label><br>
        <input type="file" id="xray_test_result" name="atk_test_result"><br><br>

        <label for="symptom_image">รูปภาพอาการ:</label><br>
        <input type="file" id="symptom_image" name="symptom_image"><br><br>

        <button type="submit">บันทึกข้อมูล</button>
    </form>
</body>
</html>
