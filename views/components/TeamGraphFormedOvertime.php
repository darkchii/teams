<?php $data_date_counts = Team::getCountsByCreationDate('day', true, false, true); ?>
<canvas id="team-creation-over-time-graph"></canvas>
<script>
    const team_creation_over_time_data = <?php echo json_encode($data_date_counts); ?>;

    const config_team_creation_over_time = {
        type: 'line',
        data: {
            labels: team_creation_over_time_data.map(item => item.date),
            datasets: [{
                label: 'Teams',
                data: team_creation_over_time_data.map(item => item.count),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                pointRadius: 0,
                fill: true
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
                        label: function (tooltipItem) {
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
                        text: 'Created'
                    },
                    beginAtZero: true
                }
            }
        }
    };

    const ctx_team_creation_over_time = document.getElementById('team-creation-over-time-graph').getContext('2d');
    const teamCreationOverTimeChart = new Chart(ctx_team_creation_over_time, config_team_creation_over_time);

    window.addEventListener('resize', function () {
        teamCreationOverTimeChart.resize();
    });
</script>