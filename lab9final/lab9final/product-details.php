<?php
session_start();
$_SESSION['user_id'] = isset($_SESSION['id_account']) ? $_SESSION['id_account'] : null; // ตั้งค่าจาก id_account

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "u299560388_651207";
$password = "PB7712Qh";
$dbname = "u299560388_651207";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$game = null; // กำหนดค่าเริ่มต้นให้ตัวแปร $game

if (isset($_GET['game_id'])) {
    $game_id = $_GET['game_id'];

    $stmt = $conn->prepare("SELECT g.game_id, g.game_name, g.developer, g.release_date, g.recommended_cpu, g.description, c.category_name, i.image
                            FROM games g 
                            JOIN game_categories c ON g.category_id = c.category_id 
                            JOIN game_images i ON g.image_game_id = i.image_game_id
                            WHERE g.game_id = :game_id");
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
}

// เช็คว่าใช้ POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['favorites'])) {
    if (isset($_SESSION['id_account'])) {
        $id_account = $_SESSION['id_account']; // รับค่า id_account จากเซสชัน
        $game_id = $_POST['game_id']; // รับค่า game_id จากแบบฟอร์ม

        // เตรียมคำสั่ง SQL เพื่อลงทะเบียนเกมในฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO favorites (id_account, game_id) VALUES (:id_account, :game_id)");
        $stmt->bindParam(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Added to favorites successfully.');</script>";
        } else {
            echo "<script>alert('Error adding to favorites.');</script>";
        }
    } else {
        echo "<script>alert('Please log in to add to favorites.');</script>";
        echo "<script>window.location.href = 'login_form.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>GAME DETAILS</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
</head>

<body>

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <a href="index.php" class="logo">
                        <img src="png/Remove-bg.ai_1728662103697.png" alt="" style="width: 158px;">
                    </a>
                    <ul class="nav">
                      <li><a href="index.php">Home</a></li>
                      <li><a href="shop.php">All Games</a></li>
                      <li><a href="product-details.php" class="active">Product Details</a></li>
                      <li><a href="Favarite.php">Favorite</a></li>
                      <li><a href="logout.php">Sign Out </a></li>
                  </ul>   
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h3>
            <?php 
            if ($game) {
                echo $game['game_name'];
            } else {
                echo "No game selected";
            }
            ?>
          </h3>
          <span class="breadcrumb">
            <a href="index.php">Home</a> > <a href="shop.php">All Games</a>
            <?php if ($game) { echo "> " . $game['game_name']; } ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="single-product section">
    <div class="container">
      <div class="row">
        <?php if ($game) { ?>
          <div class="col-lg-6">
            <div class="left-image">
              <img src="<?php echo $game['image']; ?>" alt="Game Image">
            </div>
          </div>
          <div class="col-lg-6 align-self-center">
            <h4><?php echo $game['game_name']; ?></h4>
            <p><?php echo $game['description']; ?></p>
            <form id="qty" action="" method="POST">
              <input type="hidden" name="game_id" value="<?php echo $game['game_id']; ?>">
              <button type="submit" name="favorites"><i class="fa fa-shopping-bag"></i> Add to favorites</button>
            </form>
            <ul>
              <li><span>Developer:</span> <?php echo $game['developer']; ?></li>
              <li><span>Release Date:</span> <?php echo $game['release_date']; ?></li>
              <li><span>Recommended CPU:</span> <?php echo $game['recommended_cpu']; ?></li>
            </ul>
          </div>
        <?php } else { ?>
          <div class="col-lg-12 text-center">
          <p>No game selected. Please select a game from the <a href="shop.php">All Games</a> page.</p>
          </div>
        <?php } ?>
        <div class="col-lg-12">
          <div class="sep"></div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="col-lg-12">
        <p>Copyright © 2024 GAMEHUB Class3 Group1. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>

</body>
</html>
