<?php
@include 'conect.php';

session_start();

if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}


?>



<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>GAMEHUB</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
<!--

TemplateMo 589 lugx gaming

https://templatemo.com/tm-589-lugx-gaming

-->
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
                      <li><a href="index.php" class="active">Home</a></li>
                      <li><a href="shop.php">All Games </a></li>
                      <li><a href="product-details.php">Product Details</a></li>
                      <li><a href="Favarite.php">favorite</a></li>
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

  <div class="main-banner">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 align-self-center">
          <div class="caption header-text">
            
   
            <h2>Welcome <span><?php echo $_SESSION['user_name'] ?></span> to... GAMEHUB</h2>
            
            <p> Your ultimate destination for thrilling online games, where adventure, strategy, and fun come together! Explore a world of exciting challenges and immersive experiences designed for gamers of all levels.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 offset-lg-2">
          <div class="right-image">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="features">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="png/ghost-solid-24.png" alt="" style="max-width: 44px;"> 
              </div>
              <h4>1000+ games</h4>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="png/free.png" alt="" style="max-width: 44px;">
              </div>
              <h4>All for free</h4>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="png/device.png" alt="" style="max-width: 44px;">
              </div>
              <h4>On my device</h4>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="png/friend.png" alt="" style="max-width: 44px;">
              </div>
              <h4>Play with friends</h4>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="section trending">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="section-heading">
            <h6>Trending</h6>
            <h2>Trending Games</h2>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="main-button">
            <a href="shop.php">View All</a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/trending-01.jpg" alt=""></a> <!-- ***** เอาจากsql ***** -->
            </div>
            <div class="down-content">
              <span class="category">Action</span>
              <h4>WARFRAME: VEILBREAKER</h4>
              <a href="shop.php"><i class="fa fa-shopping-bag"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/trending-02.jpg" alt=""></a>
          
            </div>
            <div class="down-content">
              <span class="category">RPG</span>
              <h4>Tower of Fantasy</h4>
              <a href="shop.php"><i class="fa fa-shopping-bag"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/trending-03.jpg" alt=""></a>
            
            </div>
            <div class="down-content">
              <span class="category">Sport</span>
              <h4>SUPER PEOPLE</h4>
              <a href="shop.php"><i class="fa fa-shopping-bag"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/trending-04.jpg" alt=""></a>
              
            </div>
            <div class="down-content">
              <span class="category">Horror</span>
              <h4>brawlhalla</h4>
              <a href="shop.php"><i class="fa fa-shopping-bag"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section most-played">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="section-heading">
            <h6>TOP GAMES</h6>
            <h2>Most Played</h2>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="main-button">
            <a href="shop.php">View All</a>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-01.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">Action</span>
                <h4>WARFRAME: VEILBREAKER</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-02.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">MMO</span>
                <h4>PUBG Battlegrounds</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-03.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">Adventure</span>
                <h4>Apex Legends™</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-04.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">Simulation</span>
                <h4>The Sims™ 4</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-05.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">Racing</span>
                <h4>Lost Ark</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
          <div class="item">
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/top-game-06.jpg" alt=""></a>
            </div>
            <div class="down-content">
                <span class="category">RPG</span>
                <h4>Destiny 2</h4>
                <a href="shop.php">Explore</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section categories">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <div class="section-heading">
            <h6>Categories</h6>
            <h2>Top Categories</h2>
          </div>
        </div>
        <div class="col-lg col-sm-6 col-xs-12">
          <div class="item">
            <h4>Action</h4>
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/categories-01.jpg" alt=""></a>
            </div>
          </div>
        </div>
        <div class="col-lg col-sm-6 col-xs-12">
          <div class="item">
            <h4>Adventure</h4>
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/categories-05.jpg" alt=""></a>
            </div>
          </div>
        </div>
        <div class="col-lg col-sm-6 col-xs-12">
          <div class="item">
            <h4>RPG</h4>
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/categories-03.jpg" alt=""></a>
            </div>
          </div>
        </div>
        <div class="col-lg col-sm-6 col-xs-12">
          <div class="item">
            <h4>Sports</h4>
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/categories-04.jpg" alt=""></a>
            </div>
          </div>
        </div>
        <div class="col-lg col-sm-6 col-xs-12">
          <div class="item">
            <h4>Puzzle</h4>
            <div class="thumb">
              <a href="shop.php"><img src="assets/images/categories-05.jpg" alt=""></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  

  <footer>
    <div class="container">
      <div class="col-lg-12">
        <p>Copyright © 2024 GAMEHUB Class3 Group1. All rights reserved. &nbsp;&nbsp; </p>
      </div>
    </div>
  </footer>

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
