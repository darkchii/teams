<?php $data_date_counts_deleted = Team::getCountsByCreationDate('day', true, true); ?>
<canvas id="team-deletion-date-graph"></canvas>
<script>
    const team_deletion_date_data = <?php echo json_encode($data_date_counts_deleted); ?>;

    const config_team_deletion_date = {
        type: 'bar',
        data: {
            labels: team_deletion_date_data.map(item => item.date),
            datasets: [{
                label: 'Teams',
                data: team_deletion_date_data.map(item => item.count),
                // backgroundColor: 'rgba(75, 192, 192, 0.2)',
                // borderColor: 'rgba(75, 192, 192, 1)',
                //similar scheme but red
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
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
                        text: 'Deleted'
                    },
                    beginAtZero: true
                }
            }
        }
    };

    const ctx_team_deletion_date = document.getElementById('team-deletion-date-graph').getContext('2d');
    const teamDeletionDateChart = new Chart(ctx_team_deletion_date, config_team_deletion_date);

    window.addEventListener('resize', function () {
        teamDeletionDateChart.resize();
    });
</script>