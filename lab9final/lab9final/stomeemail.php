<?php 
session_start();
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// Function to execute SQL queries
function executeQuery($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('ข้อผิดพลาดในการดำเนินการ: ' . mysqli_error($conn));
    }
    return $result;
}

// ฟังก์ชันสำหรับอัปโหลดไฟล์ภาพ
function uploadImage($file) {
    global $conn; // ใช้การเชื่อมต่อฐานข้อมูลจากไฟล์ connect.php
    $targetDir = "assets/profile/"; // โฟลเดอร์ที่เก็บรูปภาพ
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // ตรวจสอบว่าเป็นภาพหรือไม่
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return "ไฟล์ไม่ใช่รูปภาพ.";
    }

    // ตรวจสอบขนาดไฟล์
    if ($file["size"] > 500000) { // จำกัดขนาดไฟล์ที่ 500KB
        return "ขนาดไฟล์มากเกินไป.";
    }

    // อนุญาตให้เฉพาะบางประเภทไฟล์
    if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return "ขออภัย อนุญาตให้ใช้เฉพาะไฟล์ JPG, JPEG, PNG & GIF.";
    }

    // อัปโหลดไฟล์
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        // บันทึกข้อมูลรูปภาพในฐานข้อมูล
        $imageName = basename($file["name"]);
        $imageUrl = $targetFile;
        $createdAt = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO account_images (image_name, image_url, created_at) VALUES ('$imageName', '$imageUrl', '$createdAt')";
        if (executeQuery($sql)) {
            return mysqli_insert_id($conn); // คืนค่า image_account_id ที่เพิ่งถูกเพิ่ม
        } else {
            return "เกิดข้อผิดพลาดในการบันทึกข้อมูลรูปภาพ: " . mysqli_error($conn);
        }
    } else {
        return "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . print_r(error_get_last(), true);
    }
}

// จัดการการบันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape ค่าข้อมูลเพื่อลดปัญหา SQL Injection
    $name = mysqli_real_escape_string($conn, $_POST['username_account']);
    $email = mysqli_real_escape_string($conn, $_POST['email_account']);
    $password = mysqli_real_escape_string($conn, $_POST['password_account']);
    
    $imageId = null;

    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if (isset($_FILES['image_account']) && $_FILES['image_account']['size'] > 0) {
        $imageId = uploadImage($_FILES['image_account']); // เรียกฟังก์ชันอัปโหลดไฟล์
    } elseif (isset($_POST['image_account_url']) && !empty($_POST['image_account_url'])) {
        // กรณีกรอก URL รูปภาพ
        $imageUrl = mysqli_real_escape_string($conn, $_POST['image_account_url']);
        
        // บันทึกข้อมูลรูปภาพในฐานข้อมูล
        $sql = "INSERT INTO account_images (image_name, image_url, created_at) VALUES ('URL รูปภาพ', '$imageUrl', NOW())";
        if (executeQuery($sql)) {
            $imageId = mysqli_insert_id($conn); // รับ image_account_id จากการเพิ่ม URL
        }
    }

    // เพิ่มข้อมูลใหม่
    $sql = "INSERT INTO account (username_account, email_account, password_account, image_account_id)
            VALUES ('$name', '$email', '$password', " . ($imageId ? $imageId : 'NULL') . ")";
    if (executeQuery($sql)) {
        $_SESSION['message'] = 'เพิ่มข้อมูลสำเร็จ!';
    } else {
        $_SESSION['message'] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล!';
    }

    header("Location: alluser.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลอีเมล</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2 class="mt-4">เพิ่มข้อมูลอีเมล</h2>

    <!-- แสดงข้อความเมื่อบันทึกข้อมูลสำเร็จ -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่มข้อมูล -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username_account">ชื่อ</label>
            <input type="text" class="form-control" id="username_account" name="username_account" required>
        </div>
        <div class="form-group">
            <label for="email_account">อีเมล</label>
            <input type="email" class="form-control" id="email_account" name="email_account" required>
        </div>
        <div class="form-group">
            <label for="password_account">รหัสผ่าน</label>
            <input type="password" class="form-control" id="password_account" name="password_account" required>
        </div>
        <div class="form-group">
            <label for="image_account">อัปโหลดรูปภาพ</label>
            <input type="file" class="form-control" id="image_account" name="image_account">
            <small class="form-text text-muted">หรือ</small>
        </div>
        <div class="form-group">
            <label for="image_account_url">กรอก URL รูปภาพ</label>
            <input type="text" class="form-control" id="image_account_url" name="image_account_url" placeholder="กรุณากรอก URL ถ้าไม่อัปโหลดรูป">
        </div>
        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
