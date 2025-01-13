<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['id_account'])) {
    die("You must be logged in to view this page.");
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "u299560388_651207";
$password = "PB7712Qh";
$dbname = "u299560388_651207";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$id_account = $_SESSION['id_account'];

// ลบเกมออกจากรายการชื่นชอบ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_favorite'])) {
    $game_id = $_POST['game_id'];
    $stmt = $conn->prepare("DELETE FROM favorites WHERE id_account = :id_account AND game_id = :game_id");
    $stmt->execute(['id_account' => $id_account, 'game_id' => $game_id]);

    // รีเฟรชหน้าเพื่ออัปเดตข้อมูลใหม่
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ดึงรายการเกมชื่นชอบของผู้ใช้
$stmt = $conn->prepare("
    SELECT g.game_id, g.game_name, c.category_name, i.image 
    FROM games g 
    JOIN game_categories c ON g.category_id = c.category_id
    JOIN game_images i ON g.image_game_id = i.image_game_id
    JOIN favorites f ON g.game_id = f.game_id
    WHERE f.id_account = :id_account
");
$stmt->execute(['id_account' => $id_account]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Your Favorite Games</title>
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <style>
        .trending-box {
            padding: 50px 0;
        }

        .trending-items {
            margin-bottom: 30px;
        }

        .trending-items .thumb img {
            width: 100%;
            height: 300px;
            border-radius: 10px;
        }

        .down-content {
            text-align: center;
            padding: 15px;
        }

        .down-content h4 {
            margin-top: 10px;
            font-size: 1.5rem;
            color: #333;
        }

        .down-content p {
            color: #777;
            margin-bottom: 15px;
        }

        .down-content form {
            display: inline-block;
        }

        .down-content button {
            background-color: #ee626b;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .down-content button:hover {
            background-color: #ee626b;
        }
    </style>
</head>

<body>
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.php" class="logo">
                            <img src="png/Remove-bg.ai_1728662103697.png" alt="" style="width: 158px;">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="shop.php">All Games</a></li>
                            <li><a href="product-details.php">Product Details</a></li>
                            <li><a href="Favarite.php" class="active">Favorite</a></li>
                            <li><a href="logout.php">Sign Out <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Page Heading Start ***** -->
    <div class="page-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Your Favorite Games</h3>
                    <span class="breadcrumb"><a href="index.php">Home</a> > Your Favorite Games</span>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Page Heading End ***** -->

    <!-- ***** Favorite Games Section Start ***** -->
    <div class="container trending-box">
        <div class="row">
            <?php if (!empty($favorites)) { ?>
                <?php foreach ($favorites as $game) { ?>
                    <div class="col-lg-3 col-md-6 align-self-center trending-items">
                        <div class="item">
                            <div class="thumb">
                                <a href="product-details.php?game_id=<?php echo htmlspecialchars($game['game_id']); ?>">
                                    <img src="<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['game_name']); ?>">
                                </a>
                            </div>
                            <div class="down-content">
                                <h4><?php echo htmlspecialchars($game['game_name']); ?></h4>
                                <p><?php echo htmlspecialchars($game['category_name']); ?></p>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                    <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['game_id']); ?>">
                                    <button type="submit" name="remove_from_favorite">Remove from Favorite</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12 text-center">
                    <p>You have no favorite games yet.</p>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- ***** Favorite Games Section End ***** -->

    <!-- ***** Footer Start ***** -->
    <footer>
        <div class="container">
            <div class="col-lg-12 text-center">
            <p>Copyright © 2024 GAMEHUB Class3 Group1. All rights reserved. &nbsp;&nbsp; </p>
            </div>
        </div>
    </footer>
    <!-- ***** Footer End ***** -->

    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/counter.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>
