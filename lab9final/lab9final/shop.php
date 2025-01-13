<?php
session_start();
$_SESSION['user_id'] = $_SESSION['id_account']; // ตั้งค่าตัวแปร user_id

// Database connection
$servername = "localhost";
$username = "u299560388_651207";
$password = "PB7712Qh";

try {
    $conn = new PDO("mysql:host=$servername;dbname=u299560388_651207", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// เมื่อผู้ใช้คลิกเพิ่มเกมเป็นชื่นชอบ
if (isset($_POST['add_to_favorite'])) {
  $user_id = $_SESSION['user_id'];  // ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
  $game_id = $_POST['game_id'];

  // ตรวจสอบว่าเกมนี้ถูกเพิ่มในรายการชื่นชอบแล้วหรือไม่
  $stmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND game_id = :game_id");
  $stmt->execute(['user_id' => $user_id, 'game_id' => $game_id]);
  
  if ($stmt->rowCount() == 0) {
      // เพิ่มเกมในรายการชื่นชอบ
      $stmt = $conn->prepare("INSERT INTO favorites (user_id, game_id) VALUES (:user_id, :game_id)");
      $stmt->execute(['user_id' => $user_id, 'game_id' => $game_id]);
      echo "Game added to favorites!";
  } else {
      echo "This game is already in your favorites.";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>ALL GAMES</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>

    <style>.thumb img {
    width: 100%; /* Make the image responsive to the width of its container */
    height: 200px; /* Set a fixed height */
    object-fit: cover; /* Maintain aspect ratio and cover the area */
}  </style>
</head>


<body>

  <!-- Preloader -->
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
  <!-- Preloader End -->

  <!-- Header Area Start -->
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- Logo Start -->
                    <a href="index.php" class="logo">
                        <img src="png/Remove-bg.ai_1728662103697.png" alt="" style="width: 158px;">
                    </a>
                    <!-- Logo End -->
                    <!-- Menu Start -->
                    <ul class="nav">
                      <li><a href="index.php">Home</a></li>
                      <li><a href="shop.php" class="active">All Games</a></li>
                      <li><a href="product-details.php">Product Details</a></li>
                      <li><a href="Favarite.php">Favorite</a></li>
                      <li><a href="logout.php">Sign Out <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                    </ul>   
                    <a class="menu-trigger">
                        <span>Menu</span>
                    </a>
                    <!-- Menu End -->
                </nav>
            </div>
        </div>
    </div>
  </header>
  <!-- Header Area End -->

  <!-- Page Heading -->
  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h3>All Games</h3>
          <span class="breadcrumb"><a href="index.php">Home</a> > All Games</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Trending Section -->
  <div class="section trending">
    <div class="container">
      <ul class="trending-filter">
        <li><a class="is_active" href="#!" data-filter="*">Show All</a></li>
        <li><a href="#!" data-filter=".action">Action</a></li>
        <li><a href="#!" data-filter=".adventure">Adventure</a></li>
        <li><a href="#!" data-filter=".rpg">RPG</a></li>
        <li><a href="#!" data-filter=".strategy">Strategy</a></li>
        <li><a href="#!" data-filter=".sports">Sports</a></li>
        <li><a href="#!" data-filter=".puzzle">Puzzle</a></li>
        <li><a href="#!" data-filter=".simulation">Simulation</a></li>
        <li><a href="#!" data-filter=".horror">Horror</a></li>
        <li><a href="#!" data-filter=".fighting">Fighting</a></li>
        <li><a href="#!" data-filter=".racing">Racing</a></li>
        <li><a href="#!" data-filter=".mmo">MMO</a></li>
        <li><a href="#!" data-filter=".platformer">Platformer</a></li>
        <li><a href="#!" data-filter=".shooter">Shooter</a></li>
      </ul>

      <!-- PHP Block for Fetching Games -->
      <div class="row trending-box">
        <?php
          // Fetching games with their categories
          $stmt = $conn->query("SELECT g.game_id, g.game_name, c.category_name, i.image 
                                FROM games g 
                                JOIN game_categories c ON g.category_id = c.category_id
                                JOIN game_images i ON g.image_game_id = i.image_game_id");
          $result = $stmt->fetchAll();
          if ($result) {
            foreach($result as $row) {
              // Generate category class
              $category_class = strtolower(str_replace(' ', '-', $row['category_name']));
        ?>
              <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items <?php echo $category_class; ?>">
                <div class="item">
                  <div class="thumb img">
                    <a href="product-details.php?game_id=<?php echo $row['game_id']; ?>"><img src="<?php echo $row['image']; ?>" alt=""></a>
                  </div>
                  <div class="down-content">
                    <span class="category"><?php echo $row['category_name']; ?></span>
                    <h4><?php echo $row['game_name']; ?></h4>
                    <a href="product-details.php?game_id=<?php echo $row['game_id']; ?>"><i class="fa fa-shopping-bag"></i></a>
                  </div>
                </div>
              </div>
        <?php
            }
          } else {
            echo "<p>No games available.</p>";
          }
        ?>
      </div>

      <!-- Pagination -->
      <div class="row">
        <div class="col-lg-12">
          <ul class="pagination">
            <li><a href="#">&lt;</a></li>
            <li><a href="#">1</a></li>
            <li><a class="is_active" href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">&gt;</a></li>
          </ul>
        </div>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="col-lg-12">
      <p>Copyright © 2024 GAMEHUB Class3 Group1. All rights reserved. &nbsp;&nbsp; </p>
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

  <!-- Isotope Filter Script -->
  <script>
    var $grid = $('.trending-box').isotope({
      itemSelector: '.trending-items',
      layoutMode: 'fitRows'
    });

    // Filtering
    $('.trending-filter').on('click', 'a', function () {
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({ filter: filterValue });
    });
  </script>
  
</body>
</html>

