<?php $data_prominent_domains = Team::findMostAppearingTeamDomains(limit: 20); ?>
<canvas id="team-domains-graph"></canvas>
<script>
    const team_domains_data = <?php echo json_encode($data_prominent_domains); ?>;
    const config_names_domains = {
        type: 'bar',
        data: {
            // labels: team_creation_date_data.map(item => item.date),
            labels: Object.keys(team_domains_data),
            datasets: [{
                label: 'Teams',
                // data: team_creation_date_data.map(item => item.count),
                data: Object.values(team_domains_data),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
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
                        display: false,
                        text: 'Domain'
                    }
                },
                y: {
                    title: {
                        display: false,
                        text: 'Frequency'
                    },
                    beginAtZero: true,
                    ticks: {
                        autoSkip: false,
                        maxRotation: 90,
                        minRotation: 0
                    }
                }
            }
        }
    };
    const ctx_names_domains = document.getElementById('team-domains-graph').getContext('2d');
    const teamNamesDomainsChart = new Chart(ctx_names_domains, config_names_domains);
    window.addEventListener('resize', function () {
        teamNamesDomainsChart.resize();
    });
</script>