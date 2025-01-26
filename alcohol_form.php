<?php
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบประเมินทดสอบอาการติดสุรา</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center">แบบประเมินทดสอบอาการติดสุรา</h3>
        <form id="formqsys" name="formqsys" method="post" action="alcohol_process.php" class="mt-4">
            <table class="table table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th rowspan="2">หัวข้อการประเมิน</th>
                        <th colspan="5">มาก-น้อย</th>
                    </tr>
                    <tr>
                        <th>5</th>
                        <th>4</th>
                        <th>3</th>
                        <th>2</th>
                        <th>1</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ดึงคำถามจากฐานข้อมูล
                    $sql = "SELECT question_id, question_text FROM alcohol_assessment";
                    $result = $link->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";
                            for ($i = 5; $i >= 1; $i--) {
                                echo "<td class='text-center'>
                                    <input type='radio' name='answer_" . $row['question_id'] . "' value='$i' required>
                                  </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>ไม่พบคำถามในฐานข้อมูล</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">ส่งคำตอบ</button>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
