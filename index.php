<?php
//enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('team.php');
?>
<html>

<head>
    <title>osu! teams browser</title>

    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Filter and browse all osu! teams">
    <meta name="author" content="Amayakase">
    <meta name="keywords" content="osu!, teams, browser">
    <!-- favicon -->
    <link rel="icon" type="image/png" href="favicon.png">
</head>

<body>
    <div class="team-table-container">
        <?php
        $filter = new TeamFilter($_SERVER['QUERY_STRING']);
        $teams = Team::getTeams($filter); //returns an array of Team objects
        ?>
        <div>
            <div>
                <h1>osu! teams browser</h1>
                <div>
                    <!-- filters -->
                    <div>
                        <form method="get">
                            <input type="hidden" name="mode" value="<?php echo $filter->getMode(true); ?>">

                            <div>
                                <!-- name filter -->
                                <input type="text" name="name" placeholder="Team name"
                                    value="<?php echo $filter->getName(); ?>">
                                <!-- order by dropdown -->
                                <select name="order">
                                    <?php
                                    foreach ($team_order_options as $key => $value) {
                                        echo '<option value="' . $key . '"' . ($filter->getOrder() == $key ? ' selected' : '') . '>' . $value . '</option>';
                                    }
                                    ?>
                                </select>
                                <!-- order by direction (radio buttons) -->
                                <input type="radio" name="order_dir" value="asc" <?php echo $filter->getOrderDir() == 'asc' ? 'checked' : ''; ?>> Ascending
                                <input type="radio" name="order_dir" value="desc" <?php echo $filter->getOrderDir() == 'desc' ? 'checked' : ''; ?>> Descending
                            </div>
                            <div>
                                <table style="width:100%">
                                    <!-- small range filters -->
                                    <?php
                                    $ranges = [
                                        'members' => 'Members',
                                        'play_count' => 'Play Count',
                                        'ranked_score' => 'Ranked Score',
                                        'average_score' => 'Average Score',
                                        'performance' => 'Performance'
                                    ];
                                    //these all go next to each other
                                    echo '<tr>';
                                    foreach ($ranges as $key => $value) {
                                        // $min = $filter->getRange($key)?->getMin();
                                        // $max = $filter->getRange($key)?->getMax(false);
                                        //these all go under each other
                                        // echo '<tr>';
                                        echo '<td>' . $value . '</td>';
                                        // echo '<td><input class="team-range-filter" type="number" name="' . $key . '_min" placeholder="' . $value . ' min" value="' . $min . '"></td>';
                                        // echo '<td><input class="team-range-filter" type="number" name="' . $key . '_max" placeholder="' . $value . ' max" value="' . $max . '"></td>';
                                        // echo '</tr>';
                                    }
                                    echo '</tr>';

                                    echo '<tr>';
                                    foreach ($ranges as $key => $value) {
                                        $min = $filter->getRange($key)?->getMin(false);
                                        echo '<td><input class="team-range-filter" type="number" name="' . $key . '_min" placeholder="' . $value . ' min" value="' . $min . '"></td>';
                                    }
                                    echo '</tr>';

                                    echo '<tr>';
                                    foreach ($ranges as $key => $value) {
                                        $max = $filter->getRange($key)?->getMax(false);
                                        echo '<td><input class="team-range-filter" type="number" name="' . $key . '_max" placeholder="' . $value . ' max" value="' . $max . '"></td>';
                                    }
                                    echo '</tr>';
                                    ?>
                                </table>
                            </div>
                            <div style="margin-top:10px">
                                <input type="submit" value="Filter">
                            </div>
                        </form>
                    </div>
                    <div style="font-size: large;">
                        <?php
                        $current_url_query = $_SERVER['QUERY_STRING'];
                        foreach ($valid_modes as $index => $mode) {
                            //change the mode in the query string (or add it if it doesn't exist)
                            $query = preg_replace('/mode=[a-z]*/', 'mode=' . $mode, $current_url_query);
                            //find the first occurence of the mode in the query string, if it doesn't exist, add it
                            if (strpos($query, 'mode=') === false) {
                                $query .= '&mode=' . $mode;
                            }
                            echo '<a class="mode-select' . ($filter->getMode() == $index ? '-active' : '') . '" href="index.php?' . $query . '">' . $mode . '</a>';
                        }
                        ?>
                    </div>
                </div>
                <span>
                    Found <?php echo number_format(count($teams)); ?> teams
                </span>
                <br />
                <span style="font-size:12px">
                    If a team hasn't been seen in over 12 hours, the row will turn red and is presumed deleted.
                </span>
                <br />
                <table>
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>Team Name</th>
                        <th>Members</th>
                        <th>Play Count</th>
                        <th>Ranked Score</th>
                        <th>Average Score</th>
                        <th>Performance</th>
                        <!-- add tooltip -->
                        <th 
                            title="Last time the team was seen."
                        >Last Polled</th>
                    </tr>
                    <?php
                    foreach ($teams as $team) {
                        $exists = $team->isTeamConfirmedExisting();
                        echo '<tr class="'. ($exists ? "" : "dead-team ").'clickableRow" onclick="window.open(\'https://osu.ppy.sh/teams/' . $team->getId() . '\', \'_blank\');">';
                        echo '<td>' . $team->getId() . '</td>';
                        echo '<td style="text-align:center"><img loading="lazy" class="team-flag" src="' . $team->getFlagUrl() . '"></td>';
                        echo '<td style="max-width:300px">' . $team->getName() . '</td>';
                        echo '<td>' . number_format($team->getMembers()) . '</td>';
                        echo '<td>' . number_format($team->getRuleset()->getPlayCount()) . '</td>';
                        echo '<td>' . number_format($team->getRuleset()->getRankedScore()) . '</td>';
                        echo '<td>' . number_format($team->getRuleset()->getAverageScore()) . '</td>';
                        echo '<td>' . number_format($team->getRuleset()->getPerformance()) . '</td>';
                        echo '<td>' . get_time_ago($team->getLastUpdated()) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
</body>

</html>