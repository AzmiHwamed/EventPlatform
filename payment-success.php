    <?php
    require_once 'phpqrcode/qrlib.php';

    // Database connection setup (adjust with your real credentials)
    $host = 'localhost';
    $dbname = 'event_platform';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }

    // Get user_id and event_id from GET or set default values
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 123;
    $event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 456;

    // Fetch user from DB
    $stmtUser = $pdo->prepare("SELECT id, name, email FROM user WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    // Fetch event from DB
    $stmtEvent = $pdo->prepare("SELECT id, title, event_date FROM events WHERE id = ?");
    $stmtEvent->execute([$event_id]);
    $event = $stmtEvent->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Event not found.");
    }

    // Generate ticket content
    $ticketContent = "Ticket Info:\nUser: Addzmi\nEvent: AnimeCon\nDate: 2025-06-01";
    ob_start();
    QRcode::png($ticketContent, null, QR_ECLEVEL_L, 5);
    $imageData = ob_get_clean();
    
    
    $qrcodebase64 = "data:image/png;base64," . base64_encode($imageData);
    try {
        $stmtEvent = $pdo->prepare("INSERT INTO ticket (user_id, event_id, content) VALUES (?, ?, ?)");
        $stmtEvent->execute([$user_id, $event_id, $qrcodebase64]);
    } catch (PDOException $e) {
        // Optionally log the error message: error_log($e->getMessage());
        header('Location: ./index.php');
        exit;
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
                        <a href="./index.html">
                            <img src="img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li ><a href="./index.php">Homepage</a></li>
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

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>Ticket bought successfully</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="display:flex;flex-direction:column;align-items:center;margin-bottom:150px">
    <img src="<?php echo $qrcodebase64; ?>" />
    <p style="color:white;">Show this QR code at the event entrance.</p>    </section>

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