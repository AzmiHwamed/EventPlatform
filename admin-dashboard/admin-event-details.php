<?php
session_start();
incLude '../guard/adminauthguard.php';

if (!isset($_GET['event_id']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$event_id = intval($_GET['event_id']);
$conn = new mysqli("localhost", "root", "", "event_platform");

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

// Count users attending (from ticket table)
$ticket_count_query = $conn->prepare("SELECT COUNT(*) as ticket_count FROM ticket WHERE event_id = ?");
$ticket_count_query->bind_param("i", $event_id);
$ticket_count_query->execute();
$ticket_count_result = $ticket_count_query->get_result();
$ticket_count = $ticket_count_result->fetch_assoc()['ticket_count'];

// Calculate total money made (price * number of tickets)
$total_money = $event['price'] * $ticket_count;

// Get list of users attending
$users_query = $conn->prepare("
    SELECT user.id, user.name 
    FROM user 
    JOIN ticket ON user.id = ticket.user_id 
    WHERE ticket.event_id = ?
");
$users_query->bind_param("i", $event_id);
$users_query->execute();
$users_result = $users_query->get_result();
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anime | Admin Event Details</title>

    <!-- Google Font -->
     
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../css/plyr.css" type="text/css">
    <link rel="stylesheet" href="../css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
</head>

<body>

    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="./index.html">
                            <img src="img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li><a href="./index.php">Homepage</a></li>
                                <li><a href="./categories.php">Categories</a></li>
                                <li><a href="./MyTickets.php">My Tickets</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="header__right">
                        <a href="./login.php"><span class="icon_profile"></span></a>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>Admin Event Details</h2>
                        <p>Manage event information.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

   <!-- Admin Event Details Section Begin -->
<section class="signup spad">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="login__form p-4 shadow-sm rounded bg-white">
                    <h3 class="mb-4 text-center">Event Details - ID: <?= htmlspecialchars($event_id) ?></h3>
                    <div class="blog__details__content">
                        <div class="blog__details__text mb-4 border-bottom pb-3">
                            <h4 class="mb-3"><?= htmlspecialchars($event['title']) ?></h4>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        </div>
                        <div class="blog__details__item__text mb-4 d-flex flex-wrap justify-content-between border-bottom pb-3">
                            <p><strong>Event Date:</strong> <span class="text-primary"><?= htmlspecialchars($event['event_date']) ?></span></p>
                            <p><strong>Price per Ticket:</strong> <span class="text-success">$<?= htmlspecialchars(number_format($event['price'], 2)) ?></span></p>
                            <p><strong>Users Attending:</strong> <span class="badge badge-info"><?= htmlspecialchars($ticket_count) ?></span></p>
                            <p><strong>Total Money Made:</strong> <span class="text-danger">$<?= htmlspecialchars(number_format($total_money, 2)) ?></span></p>
                        </div>
                        <div class="blog__details__comment">
                            <h4 class="mb-3">Users Attending</h4>
                            <?php if ($users_result->num_rows > 0): ?>
                                <ul class="list-group">
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars($user['name']) ?>
                                            <span class="badge badge-secondary badge-pill">ID: <?= htmlspecialchars($user['id']) ?></span>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No users have purchased tickets yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="./index.php" class="site-btn btn-block mt-4" style="font-weight: 600; font-size: 1.1rem;">Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Admin Event Details Section End -->

    <!-- Admin Event Details Section End -->

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
                      Copyright ©<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
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