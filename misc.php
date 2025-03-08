<?php
$valid_modes = ['osu', 'taiko', 'fruits', 'mania'];


function get_time_ago($time)
{
    $time_difference = time() - $time;

    if ($time_difference < 1) {
        return 'less than 1 second ago';
    }
    $condition = array(
        12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;

        if ($d >= 1) {
            $t = round($d);
            return 'about ' . $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
        }
    }
}

class Range
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        //convert to int
        $this->min = (int) $min;
        $this->max = (int) $max;
    }

    public function getMin($keep_int_limit = true)
    {
        if (!$keep_int_limit && $this->min === 0) {
            return null;
        }
        return $this->min;
    }

    public function getMax($keep_int_limit = true)
    {
        if (!$keep_int_limit && $this->max === PHP_INT_MAX) {
            return null;
        }
        return $this->max;
    }
}