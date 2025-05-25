<?php include 'db.php';
session_start();
incLude '../guard/adminauthguard.php';

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>My Events</title>
</head>
<body>
<div class="container">
    <h1>My Events</h1>
    <a href="add_event.php">➕ Add Event</a><br><br>
    <table>
        <tr>
            <th>Title</th><th>Description</th><th>Category</th><th>Date</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM events WHERE owner_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        foreach ($stmt as $event) {
            echo "<tr>
                <td><a href='./admin-event-details.php?event_id={$event['id']}'>{$event['title']}</a></td>
                <td>{$event['description']}</td>
                <td>{$event['category_id']}</td>
                <td>{$event['event_date']}</td>
                <td>
                    <a href='edit_event.php?id={$event['id']}'>✏️</a>
                    <a href='delete_event.php?id={$event['id']}'>❌</a>
                    <a href='ban_user.php?event_id={$event['id']}'>🚫 Ban User</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
