<?php
session_start();
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับรัน SQL
function executeQuery($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// ตรวจสอบว่ามีการส่ง game_id ผ่านตัวแปร GET หรือไม่เพื่อลบข้อมูล
if (isset($_GET['delete'])) {
    $game_id = $_GET['delete'];
    
    // ตรวจสอบว่า game_id มีอยู่ในฐานข้อมูลหรือไม่ก่อนที่จะลบ
    $sql = "DELETE FROM games WHERE game_id='$game_id'";
    if (executeQuery($sql)) {
        $_SESSION['message'] = 'ลบข้อมูลเกมสำเร็จ!';
    } else {
        $_SESSION['message'] = 'เกิดข้อผิดพลาดในการลบข้อมูล!';
    }
    header('Location:allgame.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการเกม</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 30px;
            background-color: #fff;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 40px;
            text-align: center;
            font-weight: bold;
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
    
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card-body {
            padding: 20px;
        }
        .game-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .game-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .game-details {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }
        .game-actions {
            text-align: center;
        }
        .btn-warning, .btn-danger {
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 16px;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .btn-danger {
            background-color: #f44336;
            border: none;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>ระบบจัดการเกม</h2>

    <a href="addgame.php" class="btn btn-primary mb-4">เพิ่มเกม</a>
    <a href="login_form.php" class="btn btn-primary mb-4">LOGIN FOR USER</a>

    <!-- แสดงข้อความเมื่อมีการลบหรือบันทึกสำเร็จ -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Display game list in card layout -->
    <div class="row">
        <?php
        // ดึงข้อมูลจากตาราง games ร่วมกับตาราง game_images และ game_categories
        $sql = "SELECT g.game_id, g.game_name, g.release_date, g.developer, c.category_name, 
                       g.description, gi.image
                FROM games g
                LEFT JOIN game_categories c ON g.category_id = c.category_id
                LEFT JOIN game_images gi ON g.image_game_id = gi.image_game_id";
        $result = executeQuery($sql);

        // แสดงข้อมูลในรูปแบบการ์ด
        while ($game = mysqli_fetch_assoc($result)) {
            echo "<div class='col-md-4 mb-4'>";
            echo "<div class='card'>";
            echo "<img src='" . htmlspecialchars($game['image']) . "' alt='Game Image' class='game-image'>";
            echo "<div class='card-body'>";
            echo "<h5 class='game-title'>{$game['game_name']}</h5>";
            echo "<p class='game-details'>รหัสเกม: {$game['game_id']} | วันวางจำหน่าย: {$game['release_date']}</p>";
            echo "<p class='game-details'>ผู้พัฒนา: {$game['developer']} | ประเภท: {$game['category_name']}</p>";
            echo "<p class='game-details'>{$game['description']}</p>";
            echo "<div class='game-actions'>
                    <a href='gameedit.php?edit={$game['game_id']}' class='btn btn-warning'>แก้ไข</a>
                    <a href='allgame.php?delete={$game['game_id']}' class='btn btn-danger' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบข้อมูลเกมนี้?');\">ลบ</a>
                  </div>";
            echo "</div>"; // card-body
            echo "</div>"; // card
            echo "</div>"; // col-md-4
        }
        ?>
    </div>
</div>

</body>
</html>
