<?php
require_once('db.php');
require_once('misc.php');

//is a pair, key is internal name, value is display name
$team_order_options = [
    'id' => 'ID',
    'members' => 'Members',
    'play_count' => 'Play Count',
    'ranked_score' => 'Ranked Score',
    'average_score' => 'Average Score',
    'performance' => 'Performance',
    'last_updated' => 'Last Polled'
];

class TeamFilter
{
    //filter by anything (name, int min, int max)
    private $mode = 'osu';

    private $name;

    private $range_members;
    private $range_play_count;
    private $range_ranked_score;
    private $range_average_score;
    private $range_performance;

    private $order = 'performance';
    private $order_dir = 'desc';

    //getters
    public function getMode($as_string = false)
    {
        if ($as_string) {
            global $valid_modes;
            return $valid_modes[$this->mode];
        }
        return $this->mode;
    }
    public function getName()
    {
        //if name is not set or is empty, return null
        if (!$this->name || $this->name == '') {
            return null;
        }
        return $this->name;
    }
    public function getRangeMembers()
    {
        return $this->range_members;
    }
    public function getRangePlayCount()
    {
        return $this->range_play_count;
    }
    public function getRangeRankedScore()
    {
        return $this->range_ranked_score;
    }
    public function getRangeAverageScore()
    {
        return $this->range_average_score;
    }
    public function getRangePerformance()
    {
        return $this->range_performance;
    }
    public function getOrder()
    {
        return $this->order;
    }
    public function getOrderDir()
    {
        return $this->order_dir;
    }

    public function getRange($key)
    {
        return $this->{'range_' . $key};
    }

    public function __construct($url_query)
    {
        $this->processUrlQuery($url_query);
        $this->validate();
    }

    private function validate()
    {
        global $valid_modes;
        if (!in_array($this->mode, $valid_modes)) {
            die('Invalid mode');
        }

        //validate order and order_dir
        global $team_order_options;
        if (!array_key_exists($this->order, $team_order_options)) {
            die('Invalid order');
        }

        if ($this->order_dir != 'asc' && $this->order_dir != 'desc') {
            die('Invalid order direction');
        }

        //check anything for sql injection
        $all_filters = $this->getAllFilters();

        foreach ($all_filters as $key => $value) {
            if ($value) {
                if ($key == 'name') {
                    if (preg_match('/[^a-zA-Z0-9_ %\[\]\(\)%&-]/', $value)) {
                        die('Invalid name');
                    }
                } else {
                    if (!is_numeric($value->getMin()) || !is_numeric($value->getMax())) {
                        die('Invalid range');
                    }
                }
            }
        }

        //convert it to the index in valid_modes
        $this->mode = array_search($this->mode, $valid_modes);
    }

    private function processUrlQuery($query)
    {
        //find stuff like members_min=1&members_max=5, etc
        //if members_min is set but members_max isn't, set members_max to PHP_INT_MAX
        //if members_max is set but members_min isn't, set members_min to 0

        //process query
        $query = explode('&', $query);

        foreach ($query as $q) {
            $q = explode('=', $q);
            if (count($q) == 2) {
                $key = $q[0];
                $value = $q[1];
                if ($value == '') {
                    continue;
                }

                if ($key == 'members_min') {
                    $this->range_members = new Range($value, PHP_INT_MAX);
                } else if ($key == 'members_max') {
                    $this->range_members = new Range(0, $value);
                } else if ($key == 'play_count_min') {
                    $this->range_play_count = new Range($value, PHP_INT_MAX);
                } else if ($key == 'play_count_max') {
                    $this->range_play_count = new Range(0, $value);
                } else if ($key == 'ranked_score_min') {
                    $this->range_ranked_score = new Range($value, PHP_INT_MAX);
                } else if ($key == 'ranked_score_max') {
                    $this->range_ranked_score = new Range(0, $value);
                } else if ($key == 'average_score_min') {
                    $this->range_average_score = new Range($value, PHP_INT_MAX);
                } else if ($key == 'average_score_max') {
                    $this->range_average_score = new Range(0, $value);
                } else if ($key == 'performance_min') {
                    $this->range_performance = new Range($value, PHP_INT_MAX);
                } else if ($key == 'performance_max') {
                    $this->range_performance = new Range(0, $value);
                } else if ($key == 'order') {
                    $this->order = $value;
                } else if ($key == 'order_dir') {
                    $this->order_dir = $value;
                } else if ($key == 'mode') {
                    $this->mode = $value;
                } else if ($key == 'name') {
                    $this->name = $value;
                }
            }
        }
    }

    private function getAllFilters()
    {
        return [
            'members' => $this->range_members,
            'play_count' => $this->range_play_count,
            'ranked_score' => $this->range_ranked_score,
            'average_score' => $this->range_average_score,
            'performance' => $this->range_performance,
            //name is a bit different, we wanna find stuff close to the name
            'name' => $this->name ? '%' . $this->name . '%' : null
        ];
    }

    public function getSqlQueryFilters()
    {
        //generate array of filters
        $filters = [];
        $all_filters = $this->getAllFilters();

        foreach ($all_filters as $key => $value) {
            if ($value) {
                if ($key == 'name') {
                    $filters[] = $key . ' LIKE "' . $value . '"';
                } else {
                    $filters[] = $key . ' BETWEEN ' . $value->getMin() . ' AND ' . $value->getMax();
                }
            }
        }

        return $filters;
    }

    public function getSqlQueryOrder()
    {
        if($this->order == 'id') {
            return 'osu_teams.id';
        }
        return $this->order;
    }
}

class Team
{
    private $id;
    private $name;
    private $ruleset;
    private $short_name;
    private $flag_url;
    private $members;
    private $last_updated;

    //getters
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getRuleset()
    {
        return $this->ruleset;
    }
    public function getShortName()
    {
        return $this->short_name;
    }
    public function getFlagUrl()
    {
        return $this->flag_url;
    }
    public function getMembers()
    {
        return $this->members;
    }
    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    public function isTeamConfirmedExisting(){
        //if last updated is over half a day ago, we can't be sure if the team still exists
        return time() - strtotime($this->last_updated) < 43200;
    }

    public function __construct($id, $name, $short_name, $flag_url, $members, $last_updated)
    {
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
        $this->flag_url = $flag_url;
        $this->members = $members;
        $this->last_updated = $last_updated;
    }

    public static function getTeams($filter)
    {
        $query_filters = $filter->getSqlQueryFilters();
        // $sql = 'SELECT * FROM osu_teams INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id AND mode = "' . $mode . '" ORDER BY performance DESC LIMIT ' . $limit;
        $sql = 'SELECT * FROM osu_teams';
        $sql .= ' INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id AND mode = "' . $filter->getMode() . '" ';
        if (count($query_filters) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $query_filters);
        }
        $sql .= ' ORDER BY ' . $filter->getSqlQueryOrder() . ' ' . $filter->getOrderDir();
        $result = DB::query($sql);

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $team = new Team($row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['last_updated']);
            $team->addRuleset(new TeamRuleset($row['id'], $row['mode'], $row['play_count'], $row['ranked_score'], $row['average_score'], $row['performance']));
            $teams[] = $team;
        }

        return $teams;
    }

    public function addRuleset($ruleset)
    {
        $this->ruleset = $ruleset;
    }
}

class TeamRuleset
{
    private $id;
    private $mode;
    private $play_count;
    private $ranked_score;
    private $average_score;
    private $performance;

    //getters
    public function getId()
    {
        return $this->id;
    }
    public function getMode()
    {
        return $this->mode;
    }
    public function getPlayCount()
    {
        return $this->play_count;
    }
    public function getRankedScore()
    {
        return $this->ranked_score;
    }
    public function getAverageScore()
    {
        return $this->average_score;
    }
    public function getPerformance()
    {
        return $this->performance;
    }

    public function __construct($id, $mode, $play_count, $ranked_score, $average_score, $performance)
    {
        $this->id = $id;
        $this->mode = $mode;
        $this->play_count = $play_count;
        $this->ranked_score = $ranked_score;
        $this->average_score = $average_score;
        $this->performance = $performance;
    }
}