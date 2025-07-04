<?php $data_member_counts = Team::getCountsByMemberCount(); ?>
<canvas id="team-member-count-graph"></canvas>
<script>
    const team_member_counts_data = <?php echo json_encode($data_member_counts); ?>;

    const config_member_count = {
        type: 'bar',
        data: {
            // labels: team_creation_date_data.map(item => item.date),
            labels: team_member_counts_data.map(item => item.member_count),
            datasets: [{
                label: 'Teams',
                // data: team_creation_date_data.map(item => item.count),
                // data: Object.values(team_domains_data),
                data: team_member_counts_data.map(item => item.count),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            // indexAxis: 'y',
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
                        text: 'Team Members',
                        display: true,
                    },
                },
                y: {
                    title: {
                        text: 'Teams',
                        display: true,
                    },
                    beginAtZero: true,
                    ticks: {
                        autoSkip: false,
                        maxRotation: 90,
                        minRotation: 0,
                    },
                }
            }
        }
    };

    const ctx_member_count = document.getElementById('team-member-count-graph').getContext('2d');
    const teamMemberCountChart = new Chart(ctx_member_count, config_member_count);

    // Resize the chart when the window is resized
    window.addEventListener('resize', function () {
        teamMemberCountChart.resize();
    });
</script>