<?php $data_date_counts = Team::getCountsByCreationDate('day', true); ?>
<canvas id="team-creation-date-graph"></canvas>
<script>
    const team_creation_date_data = <?php echo json_encode($data_date_counts); ?>;

    const config_team_creation_date = {
        type: 'bar',
        data: {
            labels: team_creation_date_data.map(item => item.date),
            datasets: [{
                label: 'Teams',
                data: team_creation_date_data.map(item => item.count),
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

    const ctx_team_creation_date = document.getElementById('team-creation-date-graph').getContext('2d');
    const teamCreationDateChart = new Chart(ctx_team_creation_date, config_team_creation_date);

    window.addEventListener('resize', function () {
        teamCreationDateChart.resize();
    });
</script>