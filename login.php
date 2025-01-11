<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username']; // รับค่าจากฟอร์ม
    $pass = $_POST['password'];

    // สร้าง SQL query
    $sql = "SELECT * FROM admin WHERE username = '$user' AND password = '$pass'"; // ใช้ SQL query แบบปกติ

    // ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
    if ($link) {
        $result = $link->query($sql); // ใช้ query แบบปกติ

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // ตรวจสอบรหัสผ่าน
            if ($pass == $row['password']) {
                header("Location: index.php");
                exit;
            } else {
                echo "Invalid username or password!";
            }
        } else {
            echo "Invalid username or password!";
        }
    } else {
        echo "ERROR: Could not connect to the database.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .gradient-custom {
            background: #6a11cb;
            background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
            background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
        }
    </style>
</head>

<body>
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your username and password!</p>

                                <!-- ฟอร์มกรอก Username และ Password -->
                                <form action="login.php" method="POST">
                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="text" id="typeUsernameX" name="username" class="form-control form-control-lg" placeholder="Username" required />
                                    </div>

                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="password" id="typePasswordX" name="password" class="form-control form-control-lg" placeholder="Password" required />
                                    </div>

                                    <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>

                                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>