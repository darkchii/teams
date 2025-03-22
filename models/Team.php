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
        return $this->rank == 0 ? '-' : '#' . $this->rank;
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
        return $this->name;
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
    public function setMembers($members)
    {
        $this->members = $members;
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

    public function __construct($rank, $id, $name, $short_name, $flag_url, $members, $deleted, $color, $is_total_team = false)
    {
        $this->rank = $rank;
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
        $this->flag_url = $flag_url;
        $this->members = $members;
        $this->deleted = $deleted;
        $this->color = $color;
        $this->is_total_team = $is_total_team;
    }

    public static function createFakeTotalTeam($teams)
    {
        $total_team = new Team(0, 0, 'Total', 'peppy', './img/wide-peppy.png', 0, false, null, true);
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
        $_order = ' ORDER BY ' . $filter->getSqlQueryOrder() . ' ' . $filter->getOrderDir();
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
        $sql .= '"'.$filter->getMode(true) .'" as mode';
        $sql .= ', rank() over (' . $_order . ') as rank FROM osu_teams';
        $sql .= ' INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id';
        if($filter->getMode() < 4){
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
            $team = new Team($row['rank'], $row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['deleted'], $row['color']);
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
                ));
            $teams[] = $team;
        }

        $team_collection = new TeamCollection();
        foreach ($teams as $team) {
            $team_collection->addTeam($team);
        }

        return $team_collection;

        // return $teams;
    }

    public static function getTeamById($query, $type = 'id'){
        if($type == 'id' && !is_numeric($query)){
            return null;
        }
        //validate $query for sql injection (we can expect a string)
        $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');

        //we wanna get all modes regardless
        $sql = 'SELECT osu_teams.* FROM osu_teams';
        // $sql .= ' WHERE osu_teams.id = ' . $id;
        switch($type){
            case 'id':
                $sql .= ' WHERE osu_teams.id = ' . $query;
                break;
            case 'short_name':
                //watch out for what quotes are used here, double quote is usually column name, single quote is usually string
                $sql .= " WHERE osu_teams.short_name = '[" . $query . "]'";
                break;
        }
        $sql .= ' AND deleted = 0';
        $sql .= ' LIMIT 1';
        $result = DB::query($sql);

        $team = null;

        if($row = $result->fetch_assoc()){
            $team = new Team($row['rank'] ?? 0, $row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['deleted'], $row['color']);
            
            //get all rulesets
            $sql = 'SELECT * FROM osu_teams_ruleset WHERE id = ' . $row['id'];
            $result = DB::query($sql);

            while($row = $result->fetch_assoc()){
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
                    ));
            }
        }
        return $team;
    }

    public function addRuleset($ruleset)
    {
        $this->ruleset = $ruleset;
    }

    public function fetchMembers(){
        $this->members_list = TeamMember::getMembers($this->id);
    }

    public function getLeader() {
        if(count($this->members_list) == 0){
            return null;
        }

        foreach($this->members_list as $member){
            if($member->getIsLeader()){
                return $member;
            }
        }
        return null;
    }
}