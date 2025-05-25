<?php
session_start();
include 'guard/authguard.php';
if (!isset($_GET['event_id'])) {
    header("Location: index.php");
    exit();
}


$event_id = intval($_GET['event_id']);
$conn = new mysqli("localhost", "root", "", "event_platform");
if(isset($_POST['comment'])){
    $message = $conn->prepare("INSERT INTO comment (user_id,event_id,content) values (?,?,?)");
    $message->bind_param('iis',$_SESSION['user_id'],$event_id,$_POST['comment']);
    $message->execute();
    header("Location: blog-details.php?event_id=$event_id");


}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$event_query = $conn->prepare("SELECT * FROM events WHERE id = ?");
$event_query->bind_param("i", $event_id);
$event_query->execute();
$event_result = $event_query->get_result();

if ($event_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$event = $event_result->fetch_assoc();

$categories_query = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$categories_query->bind_param("i", $event['category_id']);
$categories_query->execute();
$categories_result = $categories_query->get_result();

$comments_query = $conn->prepare("
    SELECT comment.*, user.name AS author_name 
    FROM comment 
    JOIN user ON comment.user_id = user.id 
    WHERE comment.event_id = ?
");
$comments_query->bind_param("i", $event_id);
$comments_query->execute();
$comments_result = $comments_query->get_result();


$ban_check = $conn->prepare("SELECT COUNT(*) FROM ban WHERE user_id = ? AND event_id = ?");
$ban_check->bind_param("ii", $_SESSION['user_id'], $event_id);
$ban_check->execute();
$ban_count = $ban_check->get_result()->fetch_array()[0];

// Check if user already bought a ticket for this event
$ticket_check = $conn->prepare("SELECT COUNT(*) FROM ticket WHERE user_id = ? AND event_id = ?");
$ticket_check->bind_param("ii", $_SESSION['user_id'], $event_id);
$ticket_check->execute();
$ticket_count = $ticket_check->get_result()->fetch_array()[0];

// Determine the button or message to display
$show_buy_button = true;
$message = '';

if ($ban_count > 0) {
    $show_buy_button = false;
    $message = "You are banned.";
} elseif ($ticket_count > 0) {
    $show_buy_button = false;
    $message = "You already bought a ticket.";
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
                                <li ><a href="./index.php">Homepage</a></li>
                                <li ><a href="./categories.php">Browse</a></li>
                                <li ><a href="./MyTickets.php">My tickets</a></li>
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

    <!-- Blog Details Section Begin -->
    <section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8">
                    <div class="blog__details__title">
                        <?php if ($categories_result->num_rows > 0): ?>
                            <?php $category = $categories_result->fetch_assoc(); ?>
                            <h6><?php echo htmlspecialchars($category['name']); ?> <span>- <?php echo htmlspecialchars($event['event_date']); ?></span></h6>
                        <?php endif; ?>
                        <h2><?php echo  htmlspecialchars($event['title']); ?></h2>
              
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="blog__details__pic">
                        <img src=<?php echo  htmlspecialchars($event['image']);?> style="height:70vh;
                        object-fit: cover;
                        object-position: center;
                        "
                         alt="">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <div class="blog__details__text">
                            <p><?php 
                            echo  htmlspecialchars($event['description']);
                            ?></p>
                        </div>
                        <div class="blog__details__item__text">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <script>
                                function initMap() {
                                    var eventLocation = { lat: <?php echo htmlspecialchars($event['latitude']); ?>, lng: <?php echo htmlspecialchars($event['longitude']); ?> };
                                    var map = new google.maps.Map(document.getElementById('map'), {
                                        zoom: 12,
                                        center: eventLocation
                                    });
                                    var marker = new google.maps.Marker({
                                        position: eventLocation,
                                        map: map
                                    });
                                }
                            </script>
                            <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTYUh0u-uCe346Uw8gyon6FwYIIvcWs6Y&callback=initMap"></script>
                        </div>
                        
                        <div class="blog__details__btns">
                        <?php if ($show_buy_button): ?>
        <a href='./payment.php?event_id=<?php echo htmlspecialchars($event_id); ?>' class="site-btn" style="width: 100%; margin-bottom: 100px; margin-top: 100px">Buy your ticket now !</a>
    <?php else: ?>
        <p style="width: 100%; margin-bottom: 100px; margin-top: 100px; text-align: center; color: #ff4444; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
                            <div class="blog__details__comment">
                                <h4><?php echo $comments_result->num_rows; ?> Comments</h4>
                                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                                    <div class="blog__details__comment__item">
                                        <div class="blog__details__comment__item__pic">
                                            <img src="img/blog/details/comment-1.png" alt="">
                                        </div>
                                        <div class="blog__details__comment__item__text">
                                            <span><?php echo htmlspecialchars($comment['created_at']); ?></span>
                                            <h5><?php echo htmlspecialchars($comment['author_name']); ?></h5>
                                            <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="blog__details__form">
                                <h4>Leave A Commnet</h4>
                                <form action="blog-details.php?event_id=<?php echo $event_id; ?>" method="POST">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <textarea name='comment' placeholder="Message"></textarea>
                                            <button type="submit" class="site-btn">Send Message</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Details Section End -->

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