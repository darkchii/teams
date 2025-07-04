<head>
    <title>osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>

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
                            <?php require_once('views/components/TeamGraphFormed.php'); ?>
                        </div>
                    </div>
                    <div style="width:50%">
                        <h6>Teams deleted over time</h6>
                        <div style="width: 100%; height: 180px;">
                            <?php require_once('views/components/TeamGraphDeleted.php'); ?>
                        </div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%;">
                    <div style="width:50%">
                        <h6>Prominent team name words</h6>
                        <div style="width: 100%; height: 260px;">
                            <?php require_once('views/components/TeamGraphWords.php'); ?>
                        </div>
                    </div>
                    <div style="width:50%">
                        <h6>Prominent team domains</h6>
                        <div style="width: 100%; height: 260px;">
                            <?php require_once('views/components/TeamGraphsProminentDomains.php'); ?>
                        </div>
                    </div>
                </div>
                <div style="width:100%">
                    <h6>Member count spread</h6>
                    <div style="width: 100%; height: 260px;">
                        <?php require_once('views/components/TeamGraphMemberCount.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>