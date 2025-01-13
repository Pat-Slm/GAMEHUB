<?php
@include 'connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['username_account']);
   $email = mysqli_real_escape_string($conn, $_POST['email_account']);
   $pass = md5($_POST['password_account']);
   $cpass = md5($_POST['cpassword']);
   $user_type = $_POST['user_type'];

   // แก้ไข query เพื่อเลือกเฉพาะ email และ password
   $select = " SELECT * FROM account WHERE email_account = '$email' && password_account = '$pass' ";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_array($result);
      $_SESSION['id_account'] = $row['id_account']; // ตั้งค่าตัวแปร session
      if($row['user_type'] == 'admin'){
         $_SESSION['admin_name'] = $row['username_account'];
         header('location:allgame.php');
      } elseif($row['user_type'] == 'user'){
         $_SESSION['user_name'] = $row['username_account'];
         header('location:index.php');
      }
   } else {
      $error[] = 'incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login form</title>

   <!-- custom css file link  -->
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

   <form action="" method="post">
      <h3>login now</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="email" name="email_account" required placeholder="enter your email">
      <input type="password" name="password_account" required placeholder="enter your password">
      <input type="submit" name="submit" value="login now" class="form-btn">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</div>

</body>
</html>