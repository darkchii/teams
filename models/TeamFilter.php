<?php
require_once('TeamMisc.php');
require_once('Range.php');

class TeamFilter
{
    //filter by anything (name, int min, int max)
    public $mode = 'osu';

    private $name;
    private $short_name;
    private $page = 1;

    private $range_id;
    private $range_members;
    private $range_play_count;
    private $range_ranked_score;
    private $range_average_score;
    private $range_performance;
    private $range_clears;
    private $range_total_ss;
    private $range_total_s;
    private $range_total_a;
    private $range_total_score;
    private $range_play_time;
    private $range_replays_watched;
    private $range_total_hits;

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
    public function getShortName()
    {
        //if short_name is not set or is empty, return null
        if (!$this->short_name || $this->short_name == '') {
            return null;
        }
        return $this->short_name;
    }
    public function getPage()
    {
        return $this->page;
    }
    public function getRangeId()
    {
        return $this->range_id;
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
    public function getRangeClears()
    {
        return $this->range_clears;
    }
    public function getRangeTotalSS()
    {
        return $this->range_total_ss;
    }
    public function getRangeTotalS()
    {
        return $this->range_total_s;
    }
    public function getRangeTotalA()
    {
        return $this->range_total_a;
    }
    public function getRangeTotalScore()
    {
        return $this->range_total_score;
    }
    public function getRangePlayTime()
    {
        return $this->range_play_time;
    }
    public function getRangeReplaysWatched()
    {
        return $this->range_replays_watched;
    }
    public function getRangeTotalHits()
    {
        return $this->range_total_hits;
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
                if ($key == 'name' || $key == 'short_name') {
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

                if ($key == 'id_min'){
                    $this->range_id = new Range($value, PHP_INT_MAX);
                } else if ($key == 'id_max') {
                    $this->range_id = new Range(0, $value);
                } else if ($key == 'members_min') {
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
                } else if ($key == 'clears_min') {
                    $this->range_clears = new Range($value, PHP_INT_MAX);
                } else if ($key == 'clears_max') {
                    $this->range_clears = new Range(0, $value);
                } else if ($key == 'total_ss_min') {
                    $this->range_total_ss = new Range($value, PHP_INT_MAX);
                } else if ($key == 'total_ss_max') {
                    $this->range_total_ss = new Range(0, $value);
                } else if ($key == 'total_s_min') {
                    $this->range_total_s = new Range($value, PHP_INT_MAX);
                } else if ($key == 'total_s_max') {
                    $this->range_total_s = new Range(0, $value);
                } else if ($key == 'total_a_min') {
                    $this->range_total_a = new Range($value, PHP_INT_MAX);
                } else if ($key == 'total_a_max') {
                    $this->range_total_a = new Range(0, $value);
                } else if ($key == 'total_score_min') {
                    $this->range_total_score = new Range($value, PHP_INT_MAX);
                } else if ($key == 'total_score_max') {
                    $this->range_total_score = new Range(0, $value);
                } else if ($key == 'play_time_min') {
                    $this->range_play_time = new Range($value, PHP_INT_MAX);
                } else if ($key == 'play_time_max') {
                    $this->range_play_time = new Range(0, $value);
                } else if ($key == 'replays_watched_min') {
                    $this->range_replays_watched = new Range($value, PHP_INT_MAX);
                } else if ($key == 'replays_watched_max') {
                    $this->range_replays_watched = new Range(0, $value);
                } else if ($key == 'total_hits_min') {
                    $this->range_total_hits = new Range($value, PHP_INT_MAX);
                } else if ($key == 'total_hits_max') {
                    $this->range_total_hits = new Range(0, $value);
                } else if ($key == 'order') {
                    $this->order = $value;
                } else if ($key == 'order_dir') {
                    $this->order_dir = $value;
                } else if ($key == 'mode') {
                    $this->mode = $value;
                } else if ($key == 'name') {
                    $this->name = $value;
                } else if ($key == 'short_name') {
                    $this->short_name = $value;
                } else if ($key == 'page') {
                    $this->page = $value;
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
            'clears' => $this->range_clears,
            'total_ss' => $this->range_total_ss,
            'total_s' => $this->range_total_s,
            'total_a' => $this->range_total_a,
            //name is a bit different, we wanna find stuff close to the name
            'name' => $this->name ? '%' . $this->name . '%' : null,
            'short_name' => $this->short_name ? '%' . $this->short_name . '%' : null
        ];
    }

    public function getSqlQueryFilters()
    {
        //generate array of filters
        $filters = [];
        $all_filters = $this->getAllFilters();

        foreach ($all_filters as $key => $value) {
            if ($value) {
                if ($key == 'name' || $key == 'short_name') {
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
        if ($this->order == 'id') {
            return 'osu_teams.id';
        }
        return $this->order;
    }
}