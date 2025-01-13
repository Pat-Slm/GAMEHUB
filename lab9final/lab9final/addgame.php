<?php    
session_start();
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับรัน SQL
function executeQuery($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die('ข้อผิดพลาดในการดำเนินการ: ' . mysqli_error($conn));
    }
    return $result;
}

// ฟังก์ชันสำหรับอัปโหลดรูปภาพ
function uploadImage($file) {
    $targetDir = "assets/gamesimages/"; // โฟลเดอร์เก็บรูปภาพ
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // ตรวจสอบว่าไฟล์เป็นรูปภาพจริงหรือไม่
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "ไฟล์ไม่ใช่รูปภาพ.";
    }

    // ตรวจสอบขนาดไฟล์ (จำกัดที่ 500KB)
    if ($file["size"] > 500000) {
        return "ขนาดไฟล์มากเกินไป.";
    }

    // อนุญาตเฉพาะไฟล์รูปบางประเภท
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return "ขออภัย อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.";
    }

    // อัปโหลดไฟล์
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile; // ส่งคืนพาธไฟล์ที่ถูกอัปโหลด
    } else {
        return "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
    }
}

// ดึงหมวดหมู่เพื่อนำมาแสดงใน checkbox
$categories = executeQuery("SELECT * FROM game_categories");

// เพิ่มข้อมูลเกมเมื่อส่งแบบฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_name = mysqli_real_escape_string($conn, $_POST['game_name']);
    $developer = mysqli_real_escape_string($conn, $_POST['developer']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $min_cpu = mysqli_real_escape_string($conn, $_POST['min_cpu']);
    $min_ram = mysqli_real_escape_string($conn, $_POST['min_ram']);
    $min_gpu = mysqli_real_escape_string($conn, $_POST['min_gpu']);
    $release_date = mysqli_real_escape_string($conn, $_POST['release_date']);
    $recommended_cpu = mysqli_real_escape_string($conn, $_POST['recommended_cpu']);
    $recommended_ram = mysqli_real_escape_string($conn, $_POST['recommended_ram']);
    $recommended_gpu = mysqli_real_escape_string($conn, $_POST['recommended_gpu']);
    $category_ids = isset($_POST['category_id']) ? implode(',', $_POST['category_id']) : '';
    
    // ตัวแปรสำหรับเก็บพาธภาพ
    $image_game_path = null;

    // ตรวจสอบว่าผู้ใช้เลือกอัปโหลดภาพหรือใส่ URL
    if (isset($_POST['image_option'])) {
        if ($_POST['image_option'] === 'upload') {
            // อัปโหลดรูปภาพหลัก
            if (!empty($_FILES['image_file']['name'])) {
                $uploadResult = uploadImage($_FILES['image_file']);
                if (strpos($uploadResult, 'ไฟล์ไม่ใช่รูปภาพ') !== false || strpos($uploadResult, 'ขนาดไฟล์มากเกินไป') !== false || strpos($uploadResult, 'อนุญาตเฉพาะไฟล์') !== false) {
                    echo '<div class="alert alert-danger" role="alert">' . $uploadResult . '</div>';
                    return; // หยุดการดำเนินการถ้ามีข้อผิดพลาด
                } else {
                    $image_game_path = $uploadResult; // เก็บพาธของรูปภาพที่อัปโหลด
                }
            }
        } elseif ($_POST['image_option'] === 'url') {
            // ใช้ URL ของรูปภาพ
            $image_game_path = mysqli_real_escape_string($conn, $_POST['image_url']);
        }
    }

    // แทรกข้อมูลภาพลงในตาราง game_images
    $image_game_id = null;
    if ($image_game_path) {
        $insertImageSql = "INSERT INTO game_images (image, created_at) VALUES ('$image_game_path', NOW())";
        executeQuery($insertImageSql);
        $image_game_id = mysqli_insert_id($conn); // รับค่า ID ของภาพที่ถูกเพิ่ม
    }

    // แทรกข้อมูลเกม
    $sql = "INSERT INTO games (game_name, developer, description, min_cpu, min_ram, min_gpu,release_date, recommended_cpu, recommended_ram, recommended_gpu, category_id, created_at, image_game_id) 
        VALUES ('$game_name', '$developer', '$description', '$min_cpu', '$min_ram', '$min_gpu','$release_date', '$recommended_cpu', '$recommended_ram', '$recommended_gpu', '$category_ids', NOW(), '$image_game_id')";
        
    executeQuery($sql);

    echo '<div class="alert alert-success" role="alert">เพิ่มเกมสำเร็จ!</div>';

    // แสดงภาพเกมถ้ามี
    if ($image_game_path) {
        echo '<div class="mt-3">';
        echo '<h5>ภาพเกม:</h5>';
        echo '<img src="' . htmlspecialchars($image_game_path) . '" alt="Game Image" style="max-width: 100%; height: 300px;">';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มเกมใหม่</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
    .container {
        margin-top: 30px;
        background-color: #fff;
        padding: 40px;
        box-shadow: 0 10px 25px rgba(0, 188, 212, 0.5); /* ใช้สีฟ้า */
        border-radius: 20px;
        transition: box-shadow 0.3s ease; /* เอฟเฟกต์เมื่อเอาเมาส์ไปวาง */
    }
    
    .form-control:hover {
        box-shadow: 5px 5px 5px #00acc1; /* เงาที่เข้มขึ้นเมื่อเมาส์อยู่บนกล่อง */
    }
    
    /* ปรับปุ่มให้สวยงาม */
    .btn-primary {
        background-color: #00bcd4; /* สีฟ้า */
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        font-size: 18px;
        transition: background-color 0.3s ease, transform 0.3s ease; /* เอฟเฟกต์การเปลี่ยนสีและการขยาย */
    }
    
    .btn-primary:hover {
        background-color: #00acc1; /* สีฟ้าที่เข้มขึ้นเมื่อเมาส์อยู่บนปุ่ม */
        transform: translateY(-2px); /* ขยับปุ่มขึ้นเล็กน้อยเมื่อเมาส์อยู่บนปุ่ม */
    }
    
    /* ปรับเอฟเฟกต์เมื่อแสดงข้อความแจ้งเตือน */
    .alert-success {
        background-color: #dff0d8;
        color: #3c763d;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        font-size: 18px;
        margin-bottom: 30px;
        transition: opacity 0.5s ease; /* เอฟเฟกต์การเปลี่ยนแปลงความโปร่งใส */
    }
    .form-control{
       
        border-radius: 10px;
        
    }
    
    .alert-success.fade-out {
        opacity: 0; /* ซ่อนข้อความแจ้งเตือน */
    }
</style>
    <script>
        function toggleImageInput() {
            const option = document.querySelector('input[name="image_option"]:checked').value;
            document.getElementById('uploadImageDiv').style.display = (option === 'upload') ? 'block' : 'none';
            document.getElementById('urlImageDiv').style.display = (option === 'url') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h2 class="mt-4">เพิ่มเกมใหม่</h2>
    <div class="form-section">
        <form method="POST" enctype="multipart/form-data">
            <a href="allgame.php" class="btn btn-primary">กลับไปหน้าแรก</a>
            <a href="gameedit.php" class="btn btn-primary">ไปหน้าแก้ไข</a>
            
            <div class="form-group">
                <label>ชื่อเกม</label>
                <input type="text" name="game_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>ผู้พัฒนา</label>
                <input type="text" name="developer" class="form-control" required>
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Minimum CPU</label>
                <input type="text" name="min_cpu" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Minimum RAM</label>
                <input type="text" name="min_ram" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Minimum GPU</label>
                <input type="text" name="min_gpu" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>วันที่วางจำหน่าย</label>
                <input type="text" name="release_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Recommended CPU</label>
                <input type="text" name="recommended_cpu" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Recommended RAM</label>
                <input type="text" name="recommended_ram" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Recommended GPU</label>
                <input type="text" name="recommended_gpu" class="form-control" required>
            </div>

            <!-- หมวดหมู่ -->
            <div class="form-group">
                <label>หมวดหมู่</label><br>
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="category_id[]" value="<?= htmlspecialchars($category['category_id']) ?>">
                        <label class="form-check-label"><?= htmlspecialchars($category['category_name']) ?></label>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- ตัวเลือกในการอัปโหลดรูปภาพหรือใส่ URL -->
            <div class="form-group">
                <label>เลือกวิธีการเพิ่มภาพ:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="image_option" value="upload" onclick="toggleImageInput()" required>
                    <label class="form-check-label">อัปโหลดรูปภาพ</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="image_option" value="url" onclick="toggleImageInput()">
                    <label class="form-check-label">ใช้ URL รูปภาพ</label>
                </div>
            </div>

            <!-- อัปโหลดภาพ -->
            <div id="uploadImageDiv" style="display:none;">
                <div class="form-group">
                    <label>อัปโหลดภาพเกมหลัก</label>
                    <input type="file" name="image_file" class="form-control-file">
                </div>
            </div>

            <!-- ใส่ URL รูปภาพ -->
            <div id="urlImageDiv" style="display:none;">
                <div class="form-group">
                    <label>ใส่ URL รูปภาพเกม</label>
                    <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">เพิ่มเกม</button>
        </form>
    </div>
</div>

<!-- เพิ่ม JavaScript สำหรับ Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
