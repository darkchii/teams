<?php
require_once('./db.php');
require_once('./misc.php');
require_once('TeamRuleset.php');
require_once('TeamCollection.php');

class Team
{
    private $rank;
    private $id;
    private $name;
    private $ruleset;
    private $short_name;
    private $flag_url;
    private $members;
    private $last_updated;
    private $deleted;
    private $is_total_team = false;

    //getters

    public function getRank()
    {
        return $this->id == 0 ? '-' : $this->rank;
    }
    public function getRankStr() {
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

    public function __construct($rank, $id, $name, $short_name, $flag_url, $members, $deleted, $is_total_team = false)
    {
        $this->rank = $rank;
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
        $this->flag_url = $flag_url;
        $this->members = $members;
        $this->deleted = $deleted;
        $this->is_total_team = $is_total_team;
    }

    public static function createFakeTotalTeam($teams)
    {
        $total_team = new Team(0, 0, 'Total', 'peppy', './img/wide-peppy.png', 0, false, true);
        $total_team->addRuleset(new TeamRuleset(0, 'osu', 0, 0, 0, 0));

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
        }
        $total_team->getRuleset()->setAverageScore($total_average_score / count($teams));

        return $total_team;
    }

    public static function getTeams($filter)
    {
        $query_filters = $filter->getSqlQueryFilters();
        $_order = ' ORDER BY ' . $filter->getSqlQueryOrder() . ' ' . $filter->getOrderDir();
        // $sql = 'SELECT * FROM osu_teams INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id AND mode = "' . $mode . '" ORDER BY performance DESC LIMIT ' . $limit;
        $sql = 'SELECT osu_teams.*, osu_teams_ruleset.*, rank() over ('.$_order.') as rank FROM osu_teams';
        $sql .= ' INNER JOIN osu_teams_ruleset ON osu_teams.id = osu_teams_ruleset.id AND mode = "' . $filter->getMode() . '" ';
        if (count($query_filters) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $query_filters);
        }
        $sql .= $_order;
        $result = DB::query($sql);

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $team = new Team($row['rank'], $row['id'], $row['name'], $row['short_name'], $row['flag_url'], $row['members'], $row['deleted']);
            $team->addRuleset(new TeamRuleset($row['id'], $row['mode'], $row['play_count'], $row['ranked_score'], $row['average_score'], $row['performance']));
            $teams[] = $team;
        }

        $team_collection = new TeamCollection();
        foreach ($teams as $team) {
            $team_collection->addTeam($team);
        }

        return $team_collection;

        // return $teams;
    }

    public function addRuleset($ruleset)
    {
        $this->ruleset = $ruleset;
    }
}