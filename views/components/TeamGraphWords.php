<?php $data_prominent_names = Team::findMostAppearingTeamNames(limit: 20); ?>
<canvas id="team-name-words-graph"></canvas>
<script>
    const team_names_words_data = <?php echo json_encode($data_prominent_names); ?>;

    const config_names_words = {
        type: 'bar',
        data: {
            // labels: team_creation_date_data.map(item => item.date),
            labels: Object.keys(team_names_words_data),
            datasets: [{
                label: 'Teams',
                // data: team_creation_date_data.map(item => item.count),
                data: Object.values(team_names_words_data),
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
                        text: 'Word'
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 90,
                        minRotation: 0
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

    const ctx_names_words = document.getElementById('team-name-words-graph').getContext('2d');
    const teamNamesWordsChart = new Chart(ctx_names_words, config_names_words);
    window.addEventListener('resize', function () {
        teamNamesWordsChart.resize();
    });
</script>