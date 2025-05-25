<?php include 'db.php';
session_start();
incLude '../guard/adminauthguard.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM events WHERE id=? AND owner_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
header("Location: index.php");
?>
