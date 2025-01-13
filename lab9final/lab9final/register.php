<?php

include 'connect.php';

if (isset($_POST['submit'])) {

    // รับค่าจากฟอร์ม
    $name = mysqli_real_escape_string($conn, $_POST['username_account']);
    $email = mysqli_real_escape_string($conn, $_POST['email_account']);
    $pass = md5($_POST['password_account']);
    $cpass = md5($_POST['cpassword']);
    $user_type = $_POST['user_type'];

    // จัดการการอัปโหลดรูปภาพ
    $image = $_FILES['account_image']['name'];
    $image_tmp_name = $_FILES['account_image']['tmp_name'];
    $image_folder = 'uploads/'.$image;

    // ตรวจสอบว่ามีอีเมลนี้อยู่ในระบบแล้วหรือไม่
    $select = "SELECT * FROM account WHERE email_account = '$email' AND password_account = '$pass'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        // ตรวจสอบความถูกต้องของรหัสผ่าน
        if ($pass != $cpass) {
            $error[] = 'Password not matched!';
        } else {
            // เพิ่มข้อมูลผู้ใช้พร้อมชื่อรูปภาพลงในฐานข้อมูล
            $insert = "INSERT INTO account (username_account, email_account, password_account, user_type, account_image)
                       VALUES ('$name', '$email', '$pass', '$user_type', '$image')";

            if (mysqli_query($conn, $insert)) {
                // ย้ายรูปภาพไปยังโฟลเดอร์ 'uploads'
                move_uploaded_file($image_tmp_name, $image_folder);
                header('location:login_form.php'); // เปลี่ยนเส้นทางไปยังหน้า Login
            } else {
                $error[] = 'Error occurred while registering the user.';
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<style>
    body {
        background: linear-gradient(to bottom, #001f3f, #004e72, #0077b6, #00a3d6, #42d9f4);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        font-family: 'Poppins', sans-serif;
    }

    .form-container {
        background: #fff;
        padding: 50px;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(30, 48, 242, 0.15);
        width: 400px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* เอฟเฟกต์ hover สำหรับคอนเทนเนอร์ */
    .form-container:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(30, 34, 227, 0.2);
    }

    .form-container h3 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #0077b6;
        transition: color 0.3s ease;
    }

    .form-container input,
    .form-container select {
        width: 95%;
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        background: #f0f0f0;
        border: 1px solid #ddd;
        font-size: 16px;
        transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container input:hover,
    .form-container select:hover {
        border-color: #0077b6;
        background: #e0f0ff;
    }

    /* เอฟเฟกต์ hover สำหรับฟอร์มอินพุต */
    input:focus, select:focus {
        border-color: #0077b6;
        box-shadow: 0 0 8px rgba(0, 119, 182, 0.3);
    }

    .form-container .form-btn {
        background: #0077b6;
        color: #fff;
        padding: 15px;
        border-radius: 8px;
        font-size: 18px;
        text-transform: uppercase;
        cursor: pointer;
        margin-top: 20px;
        transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 119, 182, 0.4); /* เงาโทนฟ้า */
    }

    /* เอฟเฟกต์ hover และ active สำหรับปุ่ม */
    .form-container .form-btn:hover {
        background: #005f8e;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 119, 182, 0.6);
    }

    .form-container .form-btn:active {
        background: #004e72;
        transform: scale(0.95);
        box-shadow: 0 4px 10px rgba(0, 119, 182, 0.7);
    }

    .form-container p {
        margin-top: 15px;
        font-size: 16px;
        color: #0077b6;
    }

    /* เอฟเฟกต์ hover สำหรับลิงก์ */
    .form-container p a {
        color: #0077b6;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .form-container p a:hover {
        color: #005f8e;
    }

    .form-container .error-msg {
        margin: 10px 0;
        padding: 12px;
        background: crimson;
        color: white;
        border-radius: 8px;
        font-size: 16px;
    }

    /* การเพิ่มเอฟเฟกต์การเคลื่อนไหวให้กับปุ่ม */
    @keyframes button-bounce {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
        100% {
            transform: translateY(0);
        }
    }

    /* ปุ่มมีการกระดอนเบา ๆ เมื่อ hover */
    .form-container .form-btn:hover {
        animation: button-bounce 0.4s ease-in-out;
    }
</style>

    


    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data"> <!-- ต้องเพิ่ม enctype="multipart/form-data" สำหรับการอัปโหลดไฟล์ -->
            <h3>Register Now</h3>
            
            <!-- แสดงข้อผิดพลาด -->
            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">'.$error.'</span>';
                }
            }
            ?>

            <input type="text" name="username_account" required placeholder="Enter your name">
            <input type="email" name="email_account" required placeholder="Enter your email">
            <input type="password" name="password_account" required placeholder="Enter your password">
            <input type="password" name="cpassword" required placeholder="Confirm your password">
            <select name="user_type">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <input type="file" name="account_image" required accept="image/*"> <!-- ฟิลด์สำหรับการอัปโหลดรูป -->
            <input type="submit" name="submit" value="Register Now" class="form-btn">
            <p>Already have an account? <a href="login_form.php">Login now</a></p>
        </form>
    </div>
</body>
</html>