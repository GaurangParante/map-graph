<?php
require_once('connect_population.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Gujrat Map with Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
        .leaflet-control-custom {
            background-color: white;
            border: 1px solid black;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        .leaflet-top.leaflet-left {
            top: 10px;
            left: 10px;
        }
    </style>
</head>

<body>
    <h1>Map of Gujrat, India</h1>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var defaultCoordinates = [22.179532, 69.489754];
        var defaultZoomLevel = 9.5;
        
        var map = L.map('map').setView(defaultCoordinates, defaultZoomLevel); // Coordinates for Gujrat, India

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

        <?php
        try {
            $query = "SELECT latitude, longitude, name FROM locations";
            $statement = $DB->query($query);
            $rows = [];

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $latitude = $row["latitude"];
                $longitude = $row["longitude"];
                $name = $row["name"];
        ?>
                L.marker([<?= $latitude ?>, <?= $longitude ?>]).addTo(map)
                    .bindPopup('<?= $name ?>')
                    .on('click', function () {
                        map.setView([<?= $latitude ?>, <?= $longitude ?>], 15); // Zoom to level 15
                    });
        <?php
            }
        } catch (PDOException $e) {
            echo '["Error", 0]'; // Fallback to avoid breaking the JS code
            echo '<p>Something went wrong!</p><p>' . $e->getMessage() . '</p>';
        }
        ?>

        // Add custom control for reset button
        L.Control.ResetView = L.Control.extend({
            onAdd: function (map) {
                var button = L.DomUtil.create('div', 'leaflet-control-custom');
                button.innerHTML = 'Reset';
                L.DomEvent.on(button, 'click', function () {
                    map.setView(defaultCoordinates, defaultZoomLevel); // Reset to default view
                });
                return button;
            }
        });

        L.control.resetView = function (opts) {
            return new L.Control.ResetView(opts);
        };

        L.control.resetView({
            position: 'topleft'
        }).addTo(map);
    </script>
</body>

</html>
