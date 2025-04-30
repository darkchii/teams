<head>
    <title>osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>

    <?php
    $data_date_counts = Team::getCountsByCreationDate('day', true);
    $data_date_counts_deleted = Team::getCountsByCreationDate('day', true, true);
    $data_member_counts = Team::getCountsByMemberCount();
    $data_prominent_names = Team::findMostAppearingTeamNames(limit: 20);
    $data_prominent_domains = Team::findMostAppearingTeamSites(limit: 20);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>

<body>
    <div class="team-table-container" style="width: 100% !important;">
        <div style="width: 100% !important;">
            <?php require_once('elements/Header.php'); ?>
            <h1>osu! teams browser</h1>
            <div id="team-graphs-container" class="team-graphs-container" style="width: 100% !important;">
                <div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%;">
                    <div style="width:50%">
                        <h6>Teams formed over time (excluding ID 1, deleted teams)</h6>
                        <div style="width: 100%; height: 180px;">
                            <canvas id="team-creation-date-graph"></canvas>
                        </div>
                    </div>
                    <div style="width:50%">
                        <h6>Teams deleted over time</h6>
                        <div style="width: 100%; height: 180px;">
                            <canvas id="team-deletion-date-graph"></canvas>
                        </div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%;">
                    <div style="width:50%">
                        <h6>Prominent team name words</h6>
                        <div style="width: 100%; height: 260px;">
                            <canvas id="team-name-words-graph"></canvas>
                        </div>
                    </div>
                    <div style="width:50%">
                        <h6>Prominent team domains</h6>
                        <div style="width: 100%; height: 260px;">
                            <canvas id="team-domains-graph"></canvas>
                        </div>
                    </div>
                </div>
                <div style="width:100%">
                    <h6>Member count spread</h6>
                    <div style="width: 100%; height: 260px;">
                        <canvas id="team-member-count-graph"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const team_creation_date_data = <?php echo json_encode($data_date_counts); ?>;
        const team_deletion_date_data = <?php echo json_encode($data_date_counts_deleted); ?>;
        const team_names_words_data = <?php echo json_encode($data_prominent_names); ?>;
        const team_domains_data = <?php echo json_encode($data_prominent_domains); ?>;
        const team_member_counts_data = <?php echo json_encode($data_member_counts); ?>;

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


        const ctx_team_creation_date = document.getElementById('team-creation-date-graph').getContext('2d');
        const ctx_team_deletion_date = document.getElementById('team-deletion-date-graph').getContext('2d');
        const ctx_names_words = document.getElementById('team-name-words-graph').getContext('2d');
        const ctx_names_domains = document.getElementById('team-domains-graph').getContext('2d');
        const ctx_member_count = document.getElementById('team-member-count-graph').getContext('2d');
        const teamCreationDateChart = new Chart(ctx_team_creation_date, config_team_creation_date);
        const teamDeletionDateChart = new Chart(ctx_team_deletion_date, config_team_deletion_date);
        const teamNamesWordsChart = new Chart(ctx_names_words, config_names_words);
        const teamNamesDomainsChart = new Chart(ctx_names_domains, config_names_domains);
        const teamMemberCountChart = new Chart(ctx_member_count, config_member_count);


        // Resize the chart when the window is resized
        window.addEventListener('resize', function () {
            teamCreationDateChart.resize();
            teamDeletionDateChart.resize();
            teamNamesWordsChart.resize();
            teamNamesDomainsChart.resize();
            teamMemberCountChart.resize();
        });
    </script>
</body>