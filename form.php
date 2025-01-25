<<<<<<< HEAD
<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>แบบประเมินทดสอบการติดบุหรี่</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h3 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
            color: #555;
        }

        td {
            font-size: 16px;
        }

        input[type='radio'] {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 1px solid darkgray;
            border-radius: 50%;
            outline: none;
            box-shadow: 0 0 5px 0px gray inset;
            cursor: pointer;
        }

        input[type='radio']:hover {
            box-shadow: 0 0 5px 0px orange inset;
        }

        input[type='radio']:before {
            content: '';
            display: block;
            width: 60%;
            height: 60%;
            margin: 20% auto;
            border-radius: 50%;
        }

        input[type='radio']:checked:before {
            background: blue;
        }

        button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>แบบประเมินทดสอบการติดบุหรี่</h3>
        <form id="formqsys" name="formqsys" method="post" action="process.php">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">หัวข้อการประเมิน</th>
                        <th colspan="5">ระดับความต้องการบุหรี่</th>
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
                    $sql = "SELECT question_id, question_text FROM smoking_assessment";
                    $result = $link->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";
                            for ($i = 5; $i >= 1; $i--) {
                                echo "<td>
                                        <input type='radio' name='answer_" . $row['question_id'] . "' value='$i' required>
                                      </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>ไม่พบคำถามในฐานข้อมูล</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div style="margin-top: 20px; text-align: center;">
                <button type="submit">ส่งคำตอบ</button>
            </div>
        </form>
    </div>
</body>

</html>
=======
<?php
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>แบบประเมินทดสอบการติดบุหรี่</title>
    <link rel="stylesheet" href="">
    <style type="text/css">
        input[type='radio'] {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 1px solid darkgray;
            border-radius: 50%;
            outline: none;
            box-shadow: 0 0 5px 0px gray inset;
        }
        input[type='radio']:hover {
            box-shadow: 0 0 5px 0px orange inset;
        }
        input[type='radio']:before {
            content: '';
            display: block;
            width: 60%;
            height: 60%;
            margin: 20% auto;
            border-radius: 50%;
        }
        input[type='radio']:checked:before {
            background: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 align="center">แบบประเมินทดสอบการติดบุหรี่</h3>
        <form id="formqsys" name="formqsys" method="post" action="process.php">
            <table width="70%" border="1" align="center" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
                <tr>
                    <td rowspan="2" align="center"><strong>หัวข้อการประเมิน</strong></td>
                    <td colspan="5" align="center"><strong>ระดับความต้องการบุหรี่</strong></td>
                </tr>
                <tr>
                    <td align="center"><strong>5</strong></td>
                    <td align="center"><strong>4</strong></td>
                    <td align="center"><strong>3</strong></td>
                    <td align="center"><strong>2</strong></td>
                    <td align="center"><strong>1</strong></td>
                </tr>

                <?php
                // ดึงคำถามจากฐานข้อมูล
                $sql = "SELECT question_id, question_text FROM smoking_assessment";
                $result = $link->query($sql); // ใช้ $link แทน $conn

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>&nbsp;" . $row['question_text'] . "</td>";

                        // แสดง radio buttons
                        for ($i = 5; $i >= 1; $i--) {
                            echo "<td align='center'>
                                    <input type='radio' name='answer_" . $row['question_id'] . "' value='$i' required>
                                  </td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' align='center'>ไม่พบคำถามในฐานข้อมูล</td></tr>";
                }
                ?>
            </table>
            <div align="center">
                <button type="submit">ส่งคำตอบ</button>
            </div>
        </form>
    </div>
</body>
</html>
>>>>>>> ec6eff4507cc2d2ad7bb6472232b6e63d305ef1f
