<?php

class TeamCollection
{
    private $teams = [];
    private $count = 0;
    private $count_dead = 0;

    public function addTeam($team, $at_start = false)
    {
        if ($at_start) {
            array_unshift($this->teams, $team);
        } else {
            $this->teams[] = $team;
        }

        if ($team->getId() != 0) {
            if ($team->getIsDeleted()) {
                $this->count_dead++;
            } else {
                $this->count++;
            }
        }
    }

    public function calculateRankings(){
        $rank = 1;
        foreach($this->teams as $team){
            if($team->getId() == 0){
                continue;
            }

            if($team->getIsDeleted()){
                $team->setRank(-1);
            }else{
                $team->setRank($rank);
                $rank++;
            }
        }
    }

    public function getTeams($page = 1, $limit = null)
    {
        if($limit == null) {
            return $this->teams;
        }

        $start = ($page - 1) * $limit;
        //end is length, not the last index
        $end = $limit;
        return array_slice($this->teams, $start, $end);
    }

    public function getTeamCount()
    {
        return $this->count;
    }

    public function getDeadTeamCount()
    {
        return $this->count_dead;
    }
}