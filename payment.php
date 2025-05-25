<?php
session_start();
$host = 'localhost';
$db   = 'event_platform';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

if (!isset($_GET['event_id']) || !isset($_SESSION['user_id'])) {
    die("Missing event ID or user not logged in.");
}

$eventId = $_GET['event_id'];
$userId = $_SESSION['user_id'];

// Get event price
$stmt = $pdo->prepare("SELECT price FROM events WHERE id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    die("Event not found.");
}

$amount = (int)($event['price'] * 100); // in cents

$apiUrl = "https://api.sandbox.konnect.network/api/v2/payments/init-payment";
$merchantApiKey = "6814c7599f3f9a0d784d6201:hbnIyRvyLH5HkOoXZj";
$orderId = uniqid("order_");

// Redirect URL after success
$redirectUrl = "http://localhost/Events/payment-success.php?event_id=$eventId&user_id=$userId&order_id=$orderId";

$data = [
    "receiverWalletId" => "68322a75d9dd82b885934948",
    "payment_method" => "card",
    "order_id" => $orderId,
    "amount" => $amount * 10,
    "currency" => "TND",
    "redirect_url" => $redirectUrl,
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-key: $merchantApiKey",
    "Content-Type: application/json",
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['payUrl'])) {
    $payUrl = htmlspecialchars($result['payUrl'], ENT_QUOTES);
    ?>    
    <iframe src="<?= $payUrl ?>" width="100%" height="100%" frameborder="0"></iframe>

    <script>
const orderId = "<?= $orderId ?>";
const ref = "<?= $result['paymentRef'] ?>"
const interval = setInterval(() => {
    fetch(`https://api.sandbox.konnect.network/api/v2/payments/${ref}`, {
        method: 'GET',
        headers: {
            'x-api-key': '6814c7599f3f9a0d784d6201:hbnIyRvyLH5HkOoXZj',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.payment && data.payment.status === 'completed') {
            clearInterval(interval);
            window.location.href = "payment-success.php?event_id=<?= $eventId ?>&user_id=<?= $userId ?>&order_id=<?= $orderId ?>";
        }
    })
    .catch(err => console.error(err));
}, 1000);
</script>

    <?php
} else {
    echo "Error creating payment:<br>";
    print_r($result);
}
?>
