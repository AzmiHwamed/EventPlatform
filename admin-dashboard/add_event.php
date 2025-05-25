<?php include 'db.php';
session_start();
incLude '../guard/adminauthguard.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Convert image to base64
    $imageBase64 = '';
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageBase64 = 'data:' . mime_content_type($_FILES['image']['tmp_name']) . ';base64,' . base64_encode($imageData);
    }

    $stmt = $pdo->prepare("INSERT INTO events (title, description, category_id, event_date, latitude, longitude, image, price, owner_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['title'], $_POST['description'], $_POST['category_id'],
        $_POST['event_date'], $_POST['latitude'], $_POST['longitude'],
        $imageBase64, $_POST['price'], $_SESSION['user_id']
    ]);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
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

<form method="POST" class="container" enctype="multipart/form-data">
    <h2>Add Event</h2>
    <input name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
<select name="category_id" class="breadcrumb__select" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
    <option value="">All Categories</option>
    <?php
    $categoryQuery = "SELECT id, name FROM categories";
    $categoryStmt = $pdo->query($categoryQuery);

    while ($category = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($category['id']) . "'>" . htmlspecialchars($category['name']) . "</option>";
    }
    ?>
</select>    
<input type="date" name="event_date" required>

<input name="latitude" id="latitude" placeholder="Latitude" style="display:none;" readonly>
<input name="longitude" id="longitude" placeholder="Longitude" style="display:none;" readonly>

<div id="map"></div>

<label style="margin-top: 10px;">Upload Image</label>
<input type="file" name="image" accept="image/*" required>

<input name="price" placeholder="Price" required>
<button type="submit">Add</button>
</form>

<script>
    let map;
    function initMap() {
        const defaultLocation = { lat: 31.963158, lng: 35.930359 }; // Amman, Jordan as default

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 8,
            mapId: 'DEMO_MAP_ID' // optional styling
        });

        let marker;

        map.addListener("click", function (e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            if (marker) {
                marker.setPosition(e.latLng);
            } else {
                marker = new google.maps.Marker({
                    position: e.latLng,
                    map: map
                });
            }
        });
    }
</script>
<!-- Replace YOUR_API_KEY with your actual Google Maps API key -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTYUh0u-uCe346Uw8gyon6FwYIIvcWs6Y&callback=initMap" async defer></script>

</body>
</html>
