<?php
// authguard.php
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header("Location: ./login.php");
    exit();
}
?>