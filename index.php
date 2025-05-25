<?php
session_start();
$conn = new mysqli("localhost", "root", "", "event_platform");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anime | Template</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/plyr.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                    <a href="./index.php" style="text-decoration: none;color:white;font-size:25px">
                            Eventy                        
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li class="active"><a href="./index.php">Homepage</a></li>
                                <li ><a href="./categories.php">Browse</a></li>
                                <li><a href="./MyTickets.php">My tickets</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                <div class="header__right">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="./logout.php"><span class="icon_profile"></span> Logout</a>
                        <?php else: ?>
                            <a href="./login.html"><span class="icon_profile"></span> Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="hero__slider owl-carousel">
                <?php
                            $sql = "SELECT id, title, description, image FROM events LIMIT 2";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                   echo "<div class='hero__items set-bg' data-setbg='".$row['image']."'>
                    <div class='row'>
                        <div class='col-lg-6'>
                            <div class='hero__text'>
                                <div class='label'>Adventure</div>
                                <h2>".$row['title']."</h2>
                                <p>After 30 days of travel across the world...</p>
                                <a href='blog-details.php?event_id=".$row['id']."'><span>Watch Now</span> <i class='fa fa-angle-right'></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                                   
                                   ";
                                }
                            } else {
                                echo '<p>No events found.</p>';
                            }

                            ?>



            </div>
        </div>
    </section>
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="trending__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Trending Now</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            $sql = "SELECT id, title, description, image FROM events LIMIT 3";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                   echo "<div class='col-lg-4 col-md-6 col-sm-6'>
                                <div class='product__item'>
                                    <div class='product__item__pic set-bg' data-setbg='".$row['image']."'>
                                        <div class='ep'>18 / 18</div>
                                        <div class='comment'><i class='fa fa-comments'></i> 11</div>
                                        <div class='view'><i class='fa fa-eye'></i> 9141</div>
                                    </div>
                                    <div class='product__item__text'>
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href='blog-details.php?event_id=".$row['id']."'>".$row['title']."</a></h5>
                                    </div>
                                </div>
                            </div>
                                   
                                   ";
                                }
                            } else {
                                echo '<p>No events found.</p>';
                            }

                            ?>
                        </div>
                    </div>
                    
                    
                    <div class="live__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Live Action</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <?php
                            // Database connection
                           

                            // Fetch 3 events
                            $sql = "SELECT id, title, description, image FROM events LIMIT 3";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                   echo "<div class='col-lg-4 col-md-6 col-sm-6'>
                                <div class='product__item'>
                                    <div class='product__item__pic set-bg' data-setbg='".$row['image']."'>
                                        <div class='ep'>18 / 18</div>
                                        <div class='comment'><i class='fa fa-comments'></i> 11</div>
                                        <div class='view'><i class='fa fa-eye'></i> 9141</div>
                                    </div>
                                    <div class='product__item__text'>
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href='blog-details.php?event_id=".$row['id']."'>".$row['title']."</a></h5>
                                    </div>
                                </div>
                            </div>
                                   
                                   ";
                                }
                            } else {
                                echo '<p>No events found.</p>';
                            }

                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Top Views</h5>
                            </div>

                            <?php
                            // Database connection
                           

                            // Fetch 3 events
                            $sql = "SELECT id, title, description, image FROM events ORDER BY event_date DESC LIMIT 3";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                   echo "<div class='filter__gallery'>
                                <div class='product__sidebar__view__item set-bg mix day years'
                                data-setbg='".$row['image']."'>
                                <h5><a href='blog-details.php?event_id=".$row['id']."'>".$row['title']."</a></h5>
                            </div>
                                   
                                   ";
                                }
                            } else {
                                echo '<p>No events found.</p>';
                            }

                            ?>


                            

                            
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>
</div>
</div>
</section>
<!-- Product Section End -->

<!-- Footer Section Begin -->
<footer class="footer">
    <div class="page-up">
        <a href="#" id="scrollToTopButton"><span class="arrow_carrot-up"></span></a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="footer__logo">
                    <a href="./index.html"><img src="img/logo.png" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="footer__nav">
                    <ul>
                        <li class="active"><a href="./index.html">Homepage</a></li>
                        <li><a href="./categories.html">Categories</a></li>
                        <li><a href="./blog.html">Our Blog</a></li>
                        <li><a href="#">Contacts</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3">
                <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>

              </div>
          </div>
      </div>
  </footer>
  <!-- Footer Section End -->

  <!-- Search model Begin -->
  <div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch"><i class="icon_close"></i></div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>
<!-- Search model end -->

<!-- Js Plugins -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/player.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/mixitup.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>


</body>

</html>