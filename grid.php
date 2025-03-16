<?php
require_once('team.php');
$filter = new TeamFilter($_SERVER['QUERY_STRING']);
$team_collection = Team::getTeams($filter); //returns an array of Team objects

//filter out teams without flag_url
$teams = array_filter($team_collection->getTeams(), function ($team) {
    return $team->getFlagUrl() != null;
});

//limit to 100
$teams = array_slice($teams, 0, 60);

//shuffle the array
shuffle($teams);
?>

<html>
    <head>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                background-color: #000;
            }

            .flag-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, 160px);
                place-items: end center;
                gap: 10px;
                padding: 10px;
            }

            .flag-img {
                /* all should be 160x80, fit if necessary */
                width: 160px;
                height: 80px;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
            }
        </style>
    </head>
    <body>
        <!-- generate a grid of flag_url images -->
        <div class="flag-grid">
            <?php
            foreach ($teams as $team) {
                //use background-image instead of img tag
                echo '<div class="flag-img" style="background-image: url(' . $team->getFlagUrl() . ');"></div>';
            }
            ?>
        </div>
    </body>
</html>