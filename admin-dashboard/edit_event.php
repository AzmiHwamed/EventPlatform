<?php include 'db.php';
session_start();
incLude '../guard/adminauthguard.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, category_id=?, event_date=?, latitude=?, longitude=? WHERE id=? AND owner_id=?");
    $stmt->execute([
        $_POST['title'], $_POST['description'], $_POST['category_id'], $_POST['event_date'],
        $_POST['latitude'], $_POST['longitude'],
        $id, $_SESSION['user_id']
    ]);
    header("Location: index.php");
    exit;
}

$event = $pdo->prepare("SELECT * FROM events WHERE id=? AND owner_id=?");
$event->execute([$id, $_SESSION['user_id']]);
$data = $event->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <style>
        body {
            background-color: #070720;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 30px auto;
            background-color: #0B0C2A;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #000;
        }
        input, textarea, button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        input, textarea {
            background-color: #1b1b3a;
            color: white;
        }
        button {
            background-color: #3a3aff;
            color: white;
            cursor: pointer;
        }
        #map {
            width: 100%;
            height: 300px;
            margin-top: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<form method="POST" class="container">
    <h2>Edit Event</h2>
    <input name="title" placeholder="Title" value="<?= htmlspecialchars($data['title']) ?>" required>
    <textarea name="description" placeholder="Description" required><?= htmlspecialchars($data['description']) ?></textarea>
    <input name="category_id" placeholder="Category ID" value="<?= htmlspecialchars($data['category_id']) ?>" required>
    <input type="date" name="event_date" value="<?= $data['event_date'] ?>" required>

    <input name="latitude" id="latitude" placeholder="Latitude" value="<?= $data['latitude'] ?>" style="display:none" readonly>
    <input name="longitude" id="longitude" placeholder="Longitude" value="<?= $data['longitude'] ?>" style="display:none" readonly>

    <div id="map"></div>

    <button type="submit">Update</button>
</form>

<script>
    let map;
    function initMap() {
        const defaultLat = parseFloat("<?= $data['latitude'] ?>") || 31.963158;
        const defaultLng = parseFloat("<?= $data['longitude'] ?>") || 35.930359;

        const defaultLocation = { lat: defaultLat, lng: defaultLng };

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 8
        });

        let marker = new google.maps.Marker({
            position: defaultLocation,
            map: map
        });

        document.getElementById('latitude').value = defaultLat;
        document.getElementById('longitude').value = defaultLng;

        map.addListener("click", function (e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            marker.setPosition(e.latLng);
        });
    }
</script>
<!-- Replace YOUR_API_KEY with your actual Google Maps API key -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTYUh0u-uCe346Uw8gyon6FwYIIvcWs6Y&callback=initMap" async defer></script>

</body>
</html>
