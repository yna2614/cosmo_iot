<!DOCTYPE html>
<html>
<head>
    <title>OpenSenseMap Sensor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .map {
            height: 400px;
            width: 100%;
            margin: 20px 0;
        }
        #countdown {
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light text-dark">
    <div class="container py-4">
        <?php include 'menu.php'; ?>
        <h2 class="mb-3">OpenSenseMap Live Dashboard</h2>
        <p id="countdown">Next refresh in: 15s</p>

        <!-- Dynamic container -->
        <div id="boxesContainer"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const boxes = [
            { id: "5eb216359724e9001c335333", country: "Korea" },
            { id: "60d81e978855dd001cf44961", country: "Germany" },
            { id: "605f317777a88b001baefc19", country: "Kazakhstan" },
            // You can add more entries like:
            // { id: "your_box_id_here", country: "Kazakhstan" }
        ];

        const container = document.getElementById("boxesContainer");
        const countdownElement = document.getElementById("countdown");

        let maps = [], markers = [], charts = [];

        function createCountrySection(country, index) {
            const div = document.createElement("div");
            div.id = country;
            div.className = "mb-5";
            div.innerHTML = `
                <h3>${country}</h3>
                <div id="info${index}" class="mb-3"></div>
                <div id="map${index}" class="map rounded shadow mb-3"></div>
                <div id="cards${index}" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"></div>
            `;
            container.appendChild(div);

            // Map setup
            const map = L.map(`map${index}`).setView([0, 0], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            maps[index] = map;
        }

        function fetchAndRenderData() {
            boxes.forEach((box, index) => {
                fetch(`https://api.opensensemap.org/boxes/${box.id}`)
                    .then(response => response.json())
                    .then(data => {
                        const [lng, lat] = data.loc[0].geometry.coordinates;
                        const info = document.getElementById(`info${index}`);
                        const cards = document.getElementById(`cards${index}`);

                        info.innerHTML = `
                            <p><strong>Box:</strong> ${data.name}</p>
                            <p>Exposure: ${data.exposure} | Model: ${data.model}</p>
                            <p><strong>Longitude:</strong> ${lng}</p>
                            <p><strong>Latitude:</strong> ${lat}</p>
                        `;

                        maps[index].setView([lat, lng], 14);
                        if (markers[index]) maps[index].removeLayer(markers[index]);
                        markers[index] = L.marker([lat, lng]).addTo(maps[index]).bindPopup(data.name).openPopup();

                        cards.innerHTML = '';
                        charts[index] = [];

                        data.sensors.forEach((sensor, i) => {
                            const value = sensor.lastMeasurement?.value ?? "N/A";
                            const time = new Date(sensor.lastMeasurement?.createdAt || Date.now()).toLocaleString();
                            const chartId = `chart${index}_${i}`;

                            const card = document.createElement("div");
                            card.className = "col";
                            card.innerHTML = `
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">${sensor.title}</h5>
                                        <p>Value: <strong>${value} ${sensor.unit}</strong><br><small>${time}</small></p>
                                        <canvas id="${chartId}" height="120"></canvas>
                                    </div>
                                </div>
                            `;
                            cards.appendChild(card);

                            const ctx = document.getElementById(chartId).getContext('2d');
                            const chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: [time],
                                    datasets: [{
                                        label: `${sensor.title}`,
                                        data: [value],
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: { legend: { display: false } },
                                    scales: {
                                        y: { beginAtZero: true }
                                    }
                                }
                            });

                            charts[index].push(chart);
                        });
                    })
                    .catch(err => {
                        document.getElementById(`info${index}`).innerHTML = `<p class="alert alert-danger">Error loading data for ${box.country}</p>`;
                        console.error(err);
                    });
            });
        }

        // Build layout dynamically
        boxes.forEach((box, i) => createCountrySection(box.country, i));
        fetchAndRenderData();

        // Countdown and auto-refresh
        let countdown = 15;
        setInterval(() => {
            countdown--;
            countdownElement.innerText = `Next refresh in: ${countdown}s`;
            if (countdown <= 0) {
                fetchAndRenderData();
                countdown = 15;
            }
        }, 1000);
    </script>
</body>
</html>
