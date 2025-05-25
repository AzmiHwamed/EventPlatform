<?php include 'db.php';
session_start();
incLude '../guard/adminauthguard.php';

$event_id = $_GET['event_id'];
$message = '';

// Handle banning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id'])) {
    $stmt = $pdo->prepare("INSERT INTO ban (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$_POST['user_id'], $event_id]);
    $message = "✅ User banned successfully.";
}

// Get all users
$users = $pdo->query("SELECT id, name FROM user")->fetchAll();
$selected_user_id = $_POST['user_id'] ?? null; // Retain the selected user ID after POST
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ban User</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #070720;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 50px auto;
        }
        select, button , a{
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            color: #000;
        }
        .select2-selection__rendered {
            color: white !important;
        }
        .message {
            background: #0B0C2A;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #aaffaa;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Ban User from Event #<?= htmlspecialchars($event_id) ?></h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" style="margin-bottom:50px">
        <label for="user_id">Select User:</label>
        <select name="user_id" id="user_id" required>
            <option value="" disabled <?= !$selected_user_id ? 'selected' : '' ?>>Select a user to ban</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= $selected_user_id == $user['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="margin-top:50px">Ban</button>
        <a style="margin-top:50px;background:red;width:98%;display:flex;justify-content:center;" href="./index.php">Back</a>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('#user_id').select2({
        placeholder: "Select a user to ban",
        allowClear: true,
        marginTop: "15px"
    });
</script>
</body>
</html>