<?php
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
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getMode()
    {
        return $this->mode;
    }
    public function setMode($mode)
    {
        $this->mode = $mode;
    }
    public function getPlayCount()
    {
        return $this->play_count;
    }
    public function setPlayCount($play_count)
    {
        $this->play_count = $play_count;
    }
    public function getRankedScore()
    {
        return $this->ranked_score;
    }
    public function setRankedScore($ranked_score)
    {
        $this->ranked_score = $ranked_score;
    }
    public function getAverageScore()
    {
        return $this->average_score;
    }
    public function setAverageScore($average_score)
    {
        $this->average_score = $average_score;
    }
    public function getPerformance()
    {
        return $this->performance;
    }
    public function setPerformance($performance)
    {
        $this->performance = $performance;
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