<?php
$valid_modes = ['osu', 'taiko', 'fruits', 'mania'];

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
        if(!$keep_int_limit && $this->min === 0){
            return null;
        }
        return $this->min;
    }

    public function getMax($keep_int_limit = true)
    {
        if(!$keep_int_limit && $this->max === PHP_INT_MAX) {
            return null;
        }
        return $this->max;
    }
}