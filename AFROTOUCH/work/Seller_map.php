<?php
session_start();
include __DIR__ . '/../config/db.php'; // adjust path if needed

// Check if seller is logged in
if (!isset($_SESSION['seller_id'])) {
    die("Seller not logged in.");
}

// Optionally fetch seller info from DB
$seller_id = $_SESSION['seller_id'];
$sql = "SELECT name, location FROM sellers WHERE id=? AND location='Cameroon'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$seller = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Live Location</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <link rel="stylesheet" href="../dashboard.php">
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    body { font-family: Arial, sans-serif; text-align: center; }
    #map { height: 500px; width: 90%; margin: 20px auto; border: 2px solid #ccc; }
    h2 { color: #333; }
  </style>
</head>
<body>
  <h2>Live Location for Seller in Cameroon</h2>
  <div id="map"></div>

  <script>
    // Initialize map centered on Cameroon
    var map = L.map('map').setView([7.3697, 12.3547], 6);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Use browser geolocation for live location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;

        L.marker([lat, lon])
          .addTo(map)
          .bindPopup("<b><?php echo $seller['name']; ?></b><br><?php echo $seller['location']; ?>")
          .openPopup();

        map.setView([lat, lon], 13); // zoom to live location
      }, function(error) {
        alert("Unable to retrieve live location: " + error.message);
      });
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  </script>
</body>
</html>


