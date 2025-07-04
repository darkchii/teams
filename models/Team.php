<?php
require_once('./db.php');
require_once('./misc.php');
require_once('TeamRuleset.php');
require_once('TeamCollection.php');
require_once('TeamMember.php');

class Team
{
    public $rank;
    public $id;
    public $name;
    public $ruleset;
    public $short_name;
    public $flag_url;
    public $members;
    public $created_at;
    public $last_updated;
    public $deleted;
    public $color;
    public $is_total_team = false;
    public $members_list = [];

    //getters

    public function getRank()
    {
        return $this->id == 0 ? '-' : $this->rank;
    }
    public function getRankStr()
    {
        return $this->rank <= 0 ? '-' : '#' . $this->rank;
    }
    public function setRank($rank)
    {
        $this->rank = $rank;
    }
    // public function getId() { return $this->id; }
    //return dash if id is 0
    public function getId()
    {
        return $this->id == 0 ? '-' : $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getRuleset()
    {
        return $this->ruleset;
    }
    public function getShortName()
    {
        return $this->short_name ? htmlspecialchars($this->short_name, ENT_QUOTES, 'UTF-8') : null;
    }
    public function getFlagUrl()
    {
        return $this->flag_url;
    }
    public function getMembers()
    {
        return $this->members;
    }
    public function setMembers($members)
    {
        $this->members = $members;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getIsTotalTeam()
    {
        return $this->is_total_team;
    }
    public function getIsDeleted()
    {
        return $this->deleted;
    }
    public function getColor()
    {
        return $this->color ?? '#ffffff';
    }

    public function get($key)
    {
        return $this->$key;
    }

    public function __construct($rank, $id, $name, $short_name, $flag_url, $members, $created_at, $deleted, $color, $is_total_team = false)
    {
        $this->rank = $rank;
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
        $this->flag_url = $flag_url;
        $this->members = $members;
        $this->created_at = $created_at;
        $this->deleted = $deleted;
        $this->color = $color;
        $this->is_total_team = $is_total_team;
    }

    public static function createFakeTotalTeam($teams)
    {
        $total_team = new Team(0, 0, 'Total', 'peppy', './img/wide-peppy.png', 0, null, false, null, true);
        $total_team->addRuleset(new TeamRuleset(0, 'osu', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0));

        $total_average_score = 0;
        foreach ($teams as $team) {
            if ($team->getIsDeleted()) {
                continue;
            }

            $total_team->getRuleset()->setPlayCount($total_team->getRuleset()->getPlayCount() + $team->getRuleset()->getPlayCount());
            $total_team->getRuleset()->setRankedScore($total_team->getRuleset()->getRankedScore() + $team->getRuleset()->getRankedScore());
            // $total_team->getRuleset()->setAverageScore($total_team->getRuleset()->getAverageScore() + $team->getRuleset()->getAverageScore());
            $total_average_score += $team->getRuleset()->getAverageScore();
            $total_team->getRuleset()->setPerformance($total_team->getRuleset()->getPerformance() + $team->getRuleset()->getPerformance());
            $total_team->setMembers($total_team->getMembers() + $team->getMembers());

            $total_team->getRuleset()->setClears($total_team->getRuleset()->getClears() + $team->getRuleset()->getClears());
            $total_team->getRuleset()->setTotalSS($total_team->getRuleset()->getTotalSS() + $team->getRuleset()->getTotalSS());
            $total_team->getRuleset()->setTotalS($total_team->getRuleset()->getTotalS() + $team->getRuleset()->getTotalS());
            $total_team->getRuleset()->setTotalA($total_team->getRuleset()->getTotalA() + $team->getRuleset()->getTotalA());
            $total_team->getRuleset()->setTotalScore($total_team->getRuleset()->getTotalScore() + $team->getRuleset()->getTotalScore());
            $total_team->getRuleset()->setPlayTime($total_team->getRuleset()->getPlayTime() + $team->getRuleset()->getPlayTime());
            $total_team->getRuleset()->setReplaysWatched($total_team->getRuleset()->getReplaysWatched() + $team->getRuleset()->getReplaysWatched());
            $total_team->getRuleset()->setTotalHits($total_team->getRuleset()->getTotalHits() + $team->getRuleset()->getTotalHits());
        }
        $total_team->getRuleset()->setAverageScore(count($teams) > 0 ? $total_average_score / count($teams) : 0);

        return $total_team;
    }

    public static function getTeams($filter)
    {
        $query_filters = $filter->getSqlQueryFilters();
        $_order = ' ORDER BY ISNULL(' . $filter->getSqlQueryOrder() . '), ' . $filter->getSqlQueryOrder() . ' ' . $filter->getOrderDir();
        // $sql = 'SELECT * FROM osu_teams INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id AND mode = "' . $mode . '" ORDER BY performance DESC LIMIT ' . $limit;
        $sql = 'SELECT osu_teams.*, ';
        $sql .= 'SUM(play_count) as play_count, ';
        $sql .= 'SUM(ranked_score) as ranked_score, ';
        $sql .= 'AVG(average_score) as average_score, ';
        $sql .= 'SUM(performance) as performance, ';
        $sql .= 'SUM(clears) as clears, ';
        $sql .= 'SUM(total_ss) as total_ss, ';
        $sql .= 'SUM(total_s) as total_s, ';
        $sql .= 'SUM(total_a) as total_a, ';
        $sql .= 'SUM(total_score) as total_score, ';
        $sql .= 'SUM(play_time) as play_time, ';
        $sql .= 'SUM(replays_watched) as replays_watched, ';
        $sql .= 'SUM(total_hits) as total_hits, ';
        //insert the mode into the query, makes it easier than adding it to ruleset etc later
        $sql .= '"' . $filter->getMode(true) . '" as mode';
        $sql .= ', rank() over (' . $_order . ') as rank FROM osu_teams';
        $sql .= ' INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id';
        if ($filter->getMode() < 4) {
            $sql .= ' AND mode = "' . $filter->getMode() . '"';
        }
        //mode is not necessarily set, if not, we want to get all rulesets join, but with the sum of each stat
        if (count($query_filters) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $query_filters);
        }
        $sql .= ' GROUP BY osu_teams.id';
        $sql .= $_order;
        $result = DB::query($sql);

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $team = new Team($row['rank'], $row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['created_at'], $row['deleted'], $row['color']);
            $team->addRuleset(
                new TeamRuleset(
                    $row['id'],
                    $row['mode'],
                    $row['play_count'],
                    $row['ranked_score'],
                    $row['average_score'],
                    $row['performance'],
                    $row['clears'],
                    $row['total_ss'],
                    $row['total_s'],
                    $row['total_a'],
                    $row['total_score'],
                    $row['play_time'],
                    $row['replays_watched'],
                    $row['total_hits'],
                )
            );
            $teams[] = $team;
        }

        $team_collection = new TeamCollection();
        foreach ($teams as $team) {
            $team_collection->addTeam($team);
        }

        $team_collection->calculateRankings();

        return $team_collection;

        // return $teams;
    }

    //get the count of teams by creation date per day and per month
    //this is used for the graph on the main page
    /**
     * Get the count of teams by creation date
     * @param string $size The size of the date (day or month)
     * @param bool $fill Whether to fill the gaps in the data
     * @return array The counts of teams by creation date
     */
    public static function getCountsByCreationDate($size = 'day', $fill = false, $deleted_only = false)
    {
        $date_to_filter = 'created_at';
        if ($deleted_only) {
            $date_to_filter = 'last_updated';
        }
        $sql = '';
        switch ($size) {
            //excluding team ID 1, its the first team made during development, which splits the data by 2 months. too much empty space
            case 'day':
                $sql = 'SELECT DATE(' . $date_to_filter . ') as date, COUNT(*) as count FROM osu_teams WHERE deleted = ' . ($deleted_only ? '1' : '0') . ' AND id != 1 GROUP BY DATE(' . $date_to_filter . ') ORDER BY DATE(' . $date_to_filter . ') DESC';
                break;
            case 'month':
                $sql = 'SELECT DATE_FORMAT(' . $date_to_filter . ', "%Y-%m") as date, COUNT(*) as count FROM osu_teams WHERE deleted = ' . ($deleted_only ? '1' : '0') . ' AND id != 1 GROUP BY DATE_FORMAT(' . $date_to_filter . ', "%Y-%m") ORDER BY DATE_FORMAT(' . $date_to_filter . ', "%Y-%m") DESC';
                break;
        }
        $result = DB::query($sql);

        $counts = [];

        while ($row = $result->fetch_assoc()) {
            $counts[] = [
                'date' => $row['date'],
                'count' => $row['count']
            ];
        }

        //if we want to fill the gaps in the data, we need to get the first and last date and fill in the gaps
        if ($fill) {
            $first_date = new DateTime($counts[count($counts) - 1]['date']);
            $last_date = new DateTime($counts[0]['date']);

            //create a date interval of 1 day or 1 month depending on the size
            $interval = new DateInterval('P1D');
            if ($size == 'month') {
                $interval = new DateInterval('P1M');
            }

            //create a date period from the first date to the last date with the interval
            $period = new DatePeriod($first_date, $interval, $last_date->modify('+1 day'));

            //loop through the period and fill in the gaps
            foreach ($period as $date) {
                $date_str = $date->format('Y-m-d');
                if ($size == 'month') {
                    $date_str = $date->format('Y-m');
                }
                //check if the date is already in the array
                if (!in_array($date_str, array_column($counts, 'date'))) {
                    $counts[] = [
                        'date' => $date_str,
                        'count' => 0
                    ];
                }
            }
        }

        //sort the array by date
        usort($counts, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $counts;
    }

    public static $max_team_members = 256;
    public static function getCountsByMemberCount()
    {
        //create subsets of equal size between 0 and 256, so we can get the count of teams with that many members

        //get count, member_count for the grouping, and a label (0-15, 16-31, etc)
        $sql = 'SELECT COUNT(*) as count, members as member_count
        FROM osu_teams WHERE deleted = 0 GROUP BY member_count ORDER BY member_count ASC';
        $result = DB::query($sql);

        $counts = [];
        foreach ($result as $row) {
            $count = $row['count'];
            $members = $row['member_count'];

            $counts[$members] = $count;
        }

        //fill in gaps
        for ($i = 0; $i <= self::$max_team_members; $i++) {
            if (!isset($counts[$i])) {
                $counts[$i] = 0;
            }
        }
        //sort by key asc
        ksort($counts);
        //convert to array of objects
        $counts = array_map(function ($key, $value) {
            return (object) [
                'member_count' => $key,
                'count' => $value
            ];
        }, array_keys($counts), $counts);

        return $counts;
    }

    public static function findMostAppearingTeamNames($limit = 10)
    {
        //not exactly names, but words in names
        //get the most common words in team names, excluding the most common words (the, a, an, etc)
        $sql = 'SELECT name FROM osu_teams WHERE deleted = false AND id != 1';

        $result = DB::query($sql);
        $counts = [];
        foreach ($result as $row) {
            $name = $row['name'];
            //split the name into words
            $words = preg_split('/\s+/', $name);
            foreach ($words as $word) {
                //remove special characters and convert to lowercase
                $word = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $word));
                if (strlen($word) < 3 || in_array($word, ['the', 'a', 'an', 'and', 'of', 'in', 'to'])) {
                    continue;
                }
                if (!isset($counts[$word])) {
                    $counts[$word] = 0;
                }
                $counts[$word]++;
            }
        }

        //sort the array by count
        arsort($counts);

        //limit the array to the top 10
        $counts = array_slice($counts, 0, $limit, true);

        return $counts;
    }

    public static function findMostAppearingTeamDomains($limit = 10)
    {
        //not exactly names, but words in names
        //get the most common websites in team urls
        //so excludes https:// and www. and .com etc and all directories
        //do this in the query, not in php, so we can use the database to do the work
        $sql = 'WITH extracted_domains AS (
            SELECT 
                LOWER(
                    REGEXP_REPLACE(
                        REGEXP_REPLACE(url, \'^https?://(www\.)?\', \'\'), 
                        \'/.*$\', \'\'
                    )
                    ) AS domain
                FROM osu_teams
            WHERE url IS NOT NULL AND url != \'\' AND deleted = false AND id != 1
        )
        SELECT 
            domain,
            COUNT(*) AS frequency
        FROM extracted_domains
        GROUP BY domain
        ORDER BY frequency DESC
        LIMIT ' . $limit;
        $result = DB::query($sql);

        $counts = [];
        foreach ($result as $row) {
            $domain = $row['domain'];
            $frequency = $row['frequency'];
            if (!isset($counts[$domain])) {
                $counts[$domain] = $frequency;
            }
        }

        arsort($counts);

        return $counts;
    }

    //Same as domains, but full links excluding https:// and www., including subdomains, query parameters, etc.
    //Allow special characters like russian characters, etc.
    //Dont lower it either
    public static function findMostAppearingTeamSites($limit = 10)
    {
        $sql = '
        WITH normalized_urls AS (
            SELECT 
                TRIM(
                    TRAILING \'/\' FROM 
                    TRIM(
                        REGEXP_REPLACE(url, \'^https?://(www\\.)?\', \'\')
                    )
                ) AS site
            FROM osu_teams
            WHERE 
                url IS NOT NULL 
                AND url != \'\' 
                AND deleted = false 
                AND id != 1
                -- More precise empty check after all trimming
                AND TRIM(TRAILING \'/\' FROM TRIM(REGEXP_REPLACE(url, \'^https?://(www\\.)?\', \'\'))) != \'\'
        )
        SELECT 
            site,
            COUNT(*) AS frequency
        FROM normalized_urls
        GROUP BY site
        ORDER BY frequency DESC
        LIMIT ' . $limit;
        $result = DB::query($sql);
        $counts = [];
        foreach ($result as $row) {
            $site = $row['site'];
            $frequency = $row['frequency'];
            if (!isset($counts[$site])) {
                $counts[$site] = $frequency;
            }
        }
        arsort($counts);
        return $counts;
    }

    public static function getTeamById($query, $type = 'id')
    {
        if ($type == 'id' && !is_numeric($query)) {
            return null;
        }
        //validate $query for sql injection (we can expect a string)
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        //we wanna get all modes regardless
        $sql = 'SELECT osu_teams.* FROM osu_teams';
        // $sql .= ' WHERE osu_teams.id = ' . $id;
        switch ($type) {
            case 'id':
                $sql .= ' WHERE osu_teams.id = ' . $query;
                break;
            case 'short_name':
                //watch out for what quotes are used here, double quote is usually column name, single quote is usually string
                $sql .= " WHERE osu_teams.short_name LIKE '" . $query . "'";
                break;
        }
        $sql .= ' AND deleted = 0';
        $sql .= ' LIMIT 1';
        $result = DB::query($sql);

        $team = null;

        if ($row = $result->fetch_assoc()) {
            $team = new Team($row['rank'] ?? 0, $row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['created_at'], $row['deleted'], $row['color']);

            //get all rulesets
            $sql = 'SELECT * FROM osu_teams_ruleset WHERE id = ' . $row['id'];
            $result = DB::query($sql);

            while ($row = $result->fetch_assoc()) {
                $team->addRuleset(
                    new TeamRuleset(
                        $row['id'],
                        $row['mode'],
                        $row['play_count'],
                        $row['ranked_score'],
                        $row['average_score'],
                        $row['performance'],
                        $row['clears'],
                        $row['total_ss'],
                        $row['total_s'],
                        $row['total_a'],
                        $row['total_score'],
                        $row['play_time'],
                        $row['replays_watched'],
                        $row['total_hits'],
                    )
                );
            }
        }
        return $team;
    }

    public function addRuleset($ruleset)
    {
        $this->ruleset = $ruleset;
    }

    public function fetchMembers()
    {
        $this->members_list = TeamMember::getMembers($this->id);
    }

    public function getLeader()
    {
        if (count($this->members_list) == 0) {
            return null;
        }

        foreach ($this->members_list as $member) {
            if ($member->getIsLeader()) {
                return $member;
            }
        }
        return null;
    }
}