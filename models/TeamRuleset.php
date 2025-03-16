<?php
class TeamRuleset
{
    private $id;
    private $mode;
    private $play_count;
    private $ranked_score;
    private $average_score;
    private $performance;
    private $clears;
    private $total_ss;
    private $total_s;
    private $total_a;
    private $total_score;
    private $play_time;
    private $replays_watched;
    private $total_hits;

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
    public function getClears()
    {
        return $this->clears;
    }
    public function setClears($clears)
    {
        $this->clears = $clears;
    }
    public function getTotalSS()
    {
        return $this->total_ss;
    }
    public function setTotalSS($total_ss)
    {
        $this->total_ss = $total_ss;
    }
    public function getTotalS()
    {
        return $this->total_s;
    }
    public function setTotalS($total_s)
    {
        $this->total_s = $total_s;
    }
    public function getTotalA()
    {
        return $this->total_a;
    }
    public function setTotalA($total_a)
    {
        $this->total_a = $total_a;
    }
    public function getTotalScore()
    {
        return $this->total_score;
    }
    public function setTotalScore($total_score)
    {
        $this->total_score = $total_score;
    }
    public function getPlayTime()
    {
        return $this->play_time;
    }
    public function setPlayTime($play_time)
    {
        $this->play_time = $play_time;
    }
    public function getReplaysWatched()
    {
        return $this->replays_watched;
    }
    public function setReplaysWatched($replays_watched)
    {
        $this->replays_watched = $replays_watched;
    }
    public function getTotalHits()
    {
        return $this->total_hits;
    }
    public function setTotalHits($total_hits)
    {
        $this->total_hits = $total_hits;
    }
    public function get($key)
    {
        return $this->$key;
    }

    public function __construct(
        $id, 
        $mode,
        $play_count, 
        $ranked_score, 
        $average_score, 
        $performance, 
        $clears, 
        $total_ss, 
        $total_s, 
        $total_a, 
        $total_score,
        $play_time,
        $replays_watched,
        $total_hits
    )
    {
        $this->id = $id;
        $this->mode = $mode;
        $this->play_count = $play_count;
        $this->ranked_score = $ranked_score;
        $this->average_score = $average_score;
        $this->performance = $performance;
        $this->clears = $clears;
        $this->total_ss = $total_ss;
        $this->total_s = $total_s;
        $this->total_a = $total_a;
        $this->total_score = $total_score;
        $this->play_time = $play_time;
        $this->replays_watched = $replays_watched;
        $this->total_hits = $total_hits;
    }
}