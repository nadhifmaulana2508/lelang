<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta dari Google Maps Link</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="w-full max-w-xl p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-3">Masukkan Link Google Maps</h2>
    <form method="POST">
        <input type="text" name="maps_link" placeholder="Paste link Google Maps" class="w-full p-2 border rounded-md mb-3">
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md">Tampilkan Peta</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["maps_link"])) {
        $url = $_POST["maps_link"];
        
        // Ambil koordinat dari URL Google Maps
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        } else {
            echo "<p class='text-red-500 mt-3'>Format URL tidak valid!</p>";
            exit;
        }
    ?>
    
    <div id="map" class="w-full h-64 mt-3 rounded-lg shadow-md"></div>
    
    <script>
        var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map)
            .bindPopup("Lokasi Anda")
            .openPopup();
    </script>

    <?php } ?>

</div>

</body>
</html>
