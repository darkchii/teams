<head>
    <title>osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>

    <?php
    $data_date_counts = Team::getCountsByCreationDate('day', true);
    // echo '<pre>' . var_export($data_date_counts, true) . '</pre>';
    ?>

    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js "></script>
</head>

<body>
    <div class="team-table-container">
        <div>
            <?php require_once('elements/Header.php'); ?>
            <h1>osu! teams browser</h1>
            <div id="team-graphs-container" class="team-graphs-container">
                <div class="team-creation-date-graph">
                    <h6>Teams formed over time (excluding ID 1, deleted teams)</h6>
                    <div style="min-width: 900px; height: 250px;">
                        <canvas id="team-creation-date-graph"></canvas>
                    </div>
                    <!-- team creation date spread -->
                    <script>
                        const data = <?php echo json_encode($data_date_counts); ?>;
                        
                        const config_team_creation_date = {
                            type: 'bar',
                            data: {
                                labels: data.map(item => item.date),
                                datasets: [{
                                    label: 'Teams',
                                    data: data.map(item => item.count),
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Date'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Number of Teams'
                                        },
                                        beginAtZero: true
                                    }
                                }
                            }
                        };

                        const ctx = document.getElementById('team-creation-date-graph').getContext('2d');
                        const teamCreationDateChart = new Chart(ctx, config_team_creation_date);

                        // Resize the chart when the window is resized
                        window.addEventListener('resize', function() {
                            teamCreationDateChart.resize();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>