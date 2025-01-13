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
    global $conn;
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
        // แทรกข้อมูลรูปภาพในฐานข้อมูล
        $sql = "INSERT INTO game_images (image, image_name, created_at) VALUES ('$targetFile', '".$file['name']."', NOW())";
        executeQuery($sql);
        return mysqli_insert_id($conn); // ส่งกลับ ID ของรูปภาพใหม่
    } else {
        return "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
    }
}

// ดึงข้อมูลเกม
$game = null;
if (isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];
    $query = "SELECT * FROM games WHERE game_id = '$game_id'";
    $game = mysqli_fetch_assoc(executeQuery($query));
}

// ดึงหมวดหมู่เพื่อนำมาแสดงใน checkbox
$categories = executeQuery("SELECT * FROM game_categories");

// อัปเดตข้อมูลเกมเมื่อส่งแบบฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];
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

    // การอัปโหลดรูปภาพใหม่
    $new_image_game_id = null;
    if (!empty($_FILES['image_file']['name'])) {
        $new_image_game_id = uploadImage($_FILES['image_file']);
        if (is_numeric($new_image_game_id)) {
            // ถ้ารูปภาพใหม่อัปโหลดสำเร็จ จะได้ ID ของรูปภาพใหม่
        } else {
            echo '<div class="alert alert-danger" role="alert">' . $new_image_game_id . '</div>'; // แสดงข้อความผิดพลาด
        }
    }

    // ถ้ามีการอัปโหลดรูปภาพใหม่
    if ($new_image_game_id) {
        // อัปเดตข้อมูลเกมโดยใช้ image_game_id ใหม่
        $sql = "UPDATE games SET 
            game_name='$game_name', 
            developer='$developer', 
            description='$description',
            min_cpu='$min_cpu',
            min_ram='$min_ram',
            min_gpu='$min_gpu',
            release_date = '$release_date',
            recommended_cpu='$recommended_cpu',
            recommended_ram='$recommended_ram',
            recommended_gpu='$recommended_gpu',
            category_id='$category_ids',
            image_game_id='$new_image_game_id'
            WHERE game_id='$game_id'";
        executeQuery($sql);
    } else {
        // อัปเดตข้อมูลเกมโดยใช้ image_game_id เดิมถ้าไม่มีการอัปโหลดรูปภาพใหม่
        $sql = "UPDATE games SET 
            game_name='$game_name', 
            developer='$developer', 
            description='$description',
            min_cpu='$min_cpu',
            min_ram='$min_ram',
            min_gpu='$min_gpu',
            release_date = '$release_date',
            recommended_cpu='$recommended_cpu',
            recommended_ram='$recommended_ram',
            recommended_gpu='$recommended_gpu',
            category_id='$category_ids'
            WHERE game_id='$game_id'";
        executeQuery($sql);
    }

    echo '<div class="alert alert-success" role="alert">อัปเดตเกมสำเร็จ!</div>';
}

// ฟอร์มแสดงข้อมูลเกม
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลเกม</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .game-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .form-section {
            margin-top: 20px;
        }
        .custom-card {
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-warning {
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3); 
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-size: 18px;
            transition: all 0.3s ease-in-out;
        
        }

        .btn-warning:hover {
            background-color: #e0a800; /* สีที่เปลี่ยนเมื่อเลื่อนเมาส์ไปที่ปุ่ม */
            border-color: #e0a800; /* สีขอบที่เปลี่ยนเมื่อเลื่อนเมาส์ไปที่ปุ่ม */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงาเมื่อเลื่อนเมาส์ */

        }
        .btn-danger {
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3); /* เงาสำหรับปุ่มลบ */
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-size: 18px;
            transition: all 0.3s ease-in-out;
            margin-top: 15px; /* เพิ่มช่องว่างจากด้านซ้าย */
        }
        .modal-confirm-delete .modal-footer .btn-danger {
            background-color: #dc3545;
        }
        .btn-primary {
            background-color: #00bcd4;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-size: 18px;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
        background-color: #00acc1; /* สีฟ้าที่เข้มขึ้นเมื่อเมาส์อยู่บนปุ่ม */
        transform: translateY(-5px); /* ขยับปุ่มขึ้นเล็กน้อยเมื่อเมาส์อยู่บนปุ่ม */
    }
        
    </style>
</head>
<body>

<div class="container">
    
    <h2 class="mt-4">แก้ไขข้อมูลเกม</h2>
    <?php if ($game): ?>
    <div class="form-section">

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- รูปภาพอยู่ด้านซ้าย -->
                <div class="col-md-4">
                    <?php
                    if ($game['image_game_id']) {
                        $image_query = "SELECT image FROM game_images WHERE image_game_id = '".$game['image_game_id']."'";
                        $image_result = executeQuery($image_query);
                        $image = mysqli_fetch_assoc($image_result);
                        if ($image && $image['image']) {
                            echo '<img src="'.htmlspecialchars($image['image']).'" class="game-image" alt="Game Image">';
                        } else {
                            echo 'ไม่มีรูปภาพ';
                        }
                    } else {
                        echo 'ไม่มีรูปภาพ';
                    }
                    ?>
                </div>
                <!-- ฟิลด์ข้อมูลอยู่ด้านขวา -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label>ชื่อเกม</label>
                        <input type="text" name="game_name" class="form-control" value="<?= htmlspecialchars($game['game_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ผู้พัฒนา</label>
                        <input type="text" name="developer" class="form-control" value="<?= htmlspecialchars($game['developer']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($game['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Minimum CPU</label>
                        <input type="text" name="min_cpu" class="form-control" value="<?= htmlspecialchars($game['min_cpu']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Minimum RAM</label>
                        <input type="text" name="min_ram" class="form-control" value="<?= htmlspecialchars($game['min_ram']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Minimum GPU</label>
                        <input type="text" name="min_gpu" class="form-control" value="<?= htmlspecialchars($game['min_gpu']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>วันที่วางจำหน่าย</label>
                        <input type="text" name="release_date" class="form-control" value="<?= htmlspecialchars($game['release_date']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Recommended CPU</label>
                        <input type="text" name="recommended_cpu" class="form-control" value="<?= htmlspecialchars($game['recommended_cpu']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Recommended RAM</label>
                        <input type="text" name="recommended_ram" class="form-control" value="<?= htmlspecialchars($game['recommended_ram']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Recommended GPU</label>
                        <input type="text" name="recommended_gpu" class="form-control" value="<?= htmlspecialchars($game['recommended_gpu']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>หมวดหมู่</label>
                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                        <div class="form-check">
                        <input type="checkbox" name="category_id[]" class="form-check-input" value="<?= $category['category_id'] ?>" 
                        <?= in_array($category['category_id'], explode(',', $game['category_id'])) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($category['category_name']) ?></label>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="form-group">
                        <label>รูปภาพใหม่ (ถ้ามี) หลังจากเปลี่ยน รูปต้องรีเซ็ต1ครั้งเพื่อให้รูปเปลี่ยน</label>
                        <input type="file" name="image_file" class="form-control">
                    </div>
            </div>
            <button type="submit" class="btn btn-primary">อัปเดตเกม</button>
            <a href="addgame.php" class="btn btn-primary">เพิ่มเกม</a>
            <a href="allgame.php" class="btn btn-primary">กลับไปหน้าแรก</a>
        </form>
    </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4" role="alert">
            กรุณากดแก้ไขอีกครั้ง เพื่อแก้ไขเกม
        </div>
    <?php endif; ?>

    <!-- แสดงรายการเกม -->
    <h3 class="mt-5">รายการเกม</h3>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>ชื่อเกม</th>
                <th>วันวางจำหน่าย</th>
                <th>ผู้พัฒนา</th>
                <th>ประเภท</th>
                <th>รายละเอียด</th>
                <th>รูปภาพ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT g.game_id, g.game_name, g.release_date, g.developer, g.category_id, g.description, gi.image
                    FROM games g
                    LEFT JOIN game_images gi ON g.image_game_id = gi.image_game_id";
            $result = executeQuery($sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['game_id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['game_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['release_date']) . '</td>';
                echo '<td>' . htmlspecialchars($row['developer']) . '</td>';
                // ดึงชื่อหมวดหมู่จาก category_id
                $category_ids = explode(',', $row['category_id']);
                $category_names = [];
                foreach ($category_ids as $cat_id) {
                    if ($cat_id) {
                        $cat_query = "SELECT category_name FROM game_categories WHERE category_id = '$cat_id'";
                        $cat_result = executeQuery($cat_query);
                        $cat = mysqli_fetch_assoc($cat_result);
                        if ($cat) {
                            $category_names[] = htmlspecialchars($cat['category_name']);
                        }
                    }
                }
                echo '<td>' . implode(', ', $category_names) . '</td>';
                echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                echo '<td>';
                if ($row['image']) {
                    echo '<img src="' . htmlspecialchars($row['image']) . '" class="game-image" alt="Game Image">';
                } else {
                    echo 'ไม่มีรูปภาพ';
                }
                echo '</td>';
                echo '<td>
                        <a href="?game_id=' . htmlspecialchars($row['game_id']) . '" class="btn btn-warning btn-sm">แก้ไข</a>
                        
                        
                      </td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- เพิ่ม JavaScript สำหรับ Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
