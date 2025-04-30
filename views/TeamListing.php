<head>
    <title>osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>
    <meta name="description" content="Filter and browse all osu! teams">
</head>

<body>
    <div class="team-table-container">
        <?php
        $MAX_TEAMS_PER_PAGE = 100;

        $filter = new TeamFilter($_SERVER['QUERY_STRING']);
        $start_time = microtime(true);
        $team_collection = Team::getTeams($filter);
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);

        // $teams[] = Team::createFakeTotalTeam($teams);
        //insert fake team at the start of the array
        $team_collection->addTeam(Team::createFakeTotalTeam($team_collection->getTeams()), true);
        ?>
        <div style="max-width:100%;">
            <?php require_once('elements/Header.php'); ?>
            <h1>osu! teams browser</h1>
            <div>
                <div>
                    <form id="filter-form" method="get">
                        <input type="hidden" name="mode" value="<?php echo $filter->getMode(true); ?>">
                        <input type="hidden" name="page" value="<?php echo $filter->getPage(); ?>">

                        <div class="team-filter-base-options">
                            <!-- name filter -->
                            <input type="text" name="name" placeholder="Team name"
                                value="<?php echo $filter->getName(); ?>">
                            <input type="text" name="short_name" placeholder="Team tag"
                                value="<?php echo $filter->getShortName(); ?>">
                            <!-- order by dropdown -->
                            <select name="order">
                                <?php
                                foreach ($team_order_options as $key => $value) {
                                    echo '<option value="' . $key . '"' . ($filter->getOrder() == $key ? ' selected' : '') . '>' . $value['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <!-- order by direction (radio buttons) -->
                            <div class="filter-input-radio">
                                <input type="radio" name="order_dir" value="asc" <?php echo $filter->getOrderDir() == 'asc' ? 'checked' : ''; ?>> Ascending
                            </div>
                            <div class="filter-input-radio">
                                <input type="radio" name="order_dir" value="desc" <?php echo $filter->getOrderDir() == 'desc' ? 'checked' : ''; ?>> Descending
                            </div>
                        </div>
                        <button style='margin-top: 5px;' for="team-filter" type="button" class="collapsible">Advanced filters</button>

                        <div class="collapsible-div" style='margin-top: 5px;' id="team-filter">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Filter</th>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Hide</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($team_order_options as $key => $value) {
                                        $min = $filter->getRange($key)?->getMin(false);
                                        $max = $filter->getRange($key)?->getMax(false);
                                        echo '<tr>';
                                        echo '<td>' . $value['name'] . '</td>';
                                        echo '<td><input class="team-range-filter" type="'.($value['type'] ?? 'number').'" name="' . $key . '_min" placeholder="Min" value="' . $min . '"></td>';
                                        echo '<td><input class="team-range-filter" type="'.($value['type'] ?? 'number').'" name="' . $key . '_max" placeholder="Max" value="' . $max . '"></td>';
                                        if ($value['can_hide']) {
                                            echo '<td><input exclude="true" is_column_hider="true" type="checkbox" name="' . $key . '_hide">Hide</td>';
                                        } else {
                                            echo '<td></td>';
                                        }
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:10px">
                            <input type="submit" value="Filter">
                        </div>
                    </form>
                </div>
                <!-- equal width for each child element -->
                <div class="mode-select-container">
                    <?php
                    $current_url_query = $_SERVER['QUERY_STRING'];
                    //move last mode to the front of the array, rest should be in the same order
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
                Found <?php echo number_format($team_collection->getTeamCount()); ?> teams
                <?php echo ($team_collection->getDeadTeamCount() > 0 ? '('.number_format($team_collection->getDeadTeamCount()).' deleted)' : ''); ?> - Execution time:
                <?php echo number_format($execution_time, 3); ?>s
            </span>
            <br />
            <span style="font-size:12px">
                A row turns red if the team has not be seen in the last fetch and is presumed deleted. If this was
                somehow a mistake, it will likely be fixed in the next fetch.
            </span>
            <br />
            <?php
            //pagination
            $page = $filter->getPage();
            $total_pages = ceil($team_collection->getTeamCount() / $MAX_TEAMS_PER_PAGE);
            $prev_page = $page - 1;
            $next_page = $page + 1;
            $prev_disabled = $prev_page < 1 ? 'disabled' : '';
            $next_disabled = $next_page > $total_pages ? 'disabled' : '';

            //function to replace the page number in the query string
            function replace_page($page, $query)
            {
                if (strpos($query, 'page=') === false) {
                    return $query . '&page=' . $page;
                }
                return preg_replace('/page=[0-9]*/', 'page=' . $page, $query);
            }

            function getPaginationElement($page, $current_page, $forced_string = null)
            {
                global $total_pages;
                $_url = replace_page($page, $_SERVER['QUERY_STRING']);
                $_disabled = ($page < 1 || $page > $total_pages || $page == $current_page) ? 'disabled' : '';
                $_tag = 'a';
                if ($_disabled) {
                    $_tag = 'span';
                }
                // return '<' . $_tag . ' class="pagination-button ' . ($_disabled ? 'pagination-button-disabled' : '') . '" href="index.php?' . $_url . '">' . ($forced_string ?? $page) . '</' . $_tag . '>';
                $element = '';
                $element .= '<' . $_tag;
                $element .= ' class="pagination-button ' . ($_disabled ? 'pagination-button-disabled' : '') . '"';
                if (!$_disabled) {
                    $element .= ' href="index.php?' . $_url . '"';
                }
                $element .= '>';
                $element .= ($forced_string ?? $page);
                $element .= '</' . $_tag . '>';
                return $element;
            }

            function getPagination() {
                global $page, $total_pages, $prev_page, $next_page, $prev_disabled, $next_disabled;
                echo '<div class="pagination">';
                echo getPaginationElement($prev_page, $page, "Previous");
                $pagination_pages = 5;
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i < $page - $pagination_pages && $i > 1) {
                        echo '<span>...</span>';
                        $i = $page - $pagination_pages;
                    }
                    if ($i > $page + $pagination_pages && $i < $total_pages) {
                        echo '<span>...</span>';
                        $i = $total_pages;
                    }
                    if ($i < 1) {
                        continue;
                    }
                    if ($i > $total_pages) {
                        break;
                    }
                    echo getPaginationElement($i, $page);
                }
                echo getPaginationElement($next_page, $page, "Next");
                echo '</div>';
            }

            //show Previous and Next buttons
            //Also show 10 pages before and after the current page (if in bounds)
            getPagination();
            ?>
            <!-- center the table horizontally -->
            <div style="overflow-x:auto;max-width:100%;">
                <table cellpadding="0">
                    <colgroup>
                        <col />
                        <col column-type="id" id="col_id" />
                        <col />
                        <col />
                        <col />
                        <col column-type="members" id="col_members" />
                        <?php
                        foreach ($team_stat_column_data as $key => $value) {
                            echo '<col class="'.(($filter->getOrder() == $key) ? 'active-sort' : 'mobile-hidden').'" column-type="' . $key . '" id="col_' . $key . '" />';
                        }
                        ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th column-type="id">ID</th>
                            <th></th>
                            <th>Tag</th>
                            <th>Team Name</th>
                            <th column-type="members">Members</th>
                            <?php
                            foreach ($team_stat_column_data as $key => $value) {
                                echo '<th class="'.(($filter->getOrder() == $key) ? 'active-sort' : 'mobile-hidden').'" column-type="' . $key . '">' . $value['name'] . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($team_collection->getTeams($filter->getPage(), $MAX_TEAMS_PER_PAGE) as $team) {
                            $exists = !$team->getIsDeleted();
                            echo '<tr class="' . ($team->getIsTotalTeam() ? "total-team " : " ") . '' . ($exists ? "" : "dead-team ") . '">';
                            echo '<td>' . $team->getRankStr() . '</td>';
                            echo '<td column-type="id">' . $team->getId() . '</td>';
                            echo '<td style="text-align:center;"><img loading="lazy" class="team-flag" src="' . $team->getFlagUrl() . '"></td>';
                            echo '<td style="' . ($team->getShortName() ? '' : 'font-style:italic;color:grey;') . '">' . ($team->getShortName() ? ('<span class="hint--top hint--no-arrow hint--no-animate" data-hint="Team color: ' . $team->getColor() . '" style="color:' . $team->getColor() . '">⬤</span> [' . $team->getShortName() . ']') : 'N/A') . '</td>';
                            echo '<td style="max-width:150px;overflow:hidden;"><a href="/teams/id/' . $team->getId() . '" target="_blank">' . $team->getName() . '</a></td>';
                            echo '<td column-type="members">' . number_format($team->getMembers()) . '</td>';
                            $data = get_team_row_data($team);
                            foreach ($data as $key => $value) {
                                // echo '<td>' . $value['value'] . '</td>';
                                echo '<td class="'.(($filter->getOrder() == $key) ? 'active-sort' : 'mobile-hidden').'" column-type="' . $key . '">';
                                if ($value['tooltip']) {
                                    echo '<span class="hint--top hint--no-arrow hint--no-animate" data-hint="' . $value['tooltip'] . '">' . $value['value'] . '</span>';
                                } else {
                                    echo $value['value'];
                                }
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            getPagination();
            ?>
        </div>
</body>