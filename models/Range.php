<?php

class Range
{
    private $min;
    private $max;
    private $is_numeric = true;

    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;

        // check if min and max are numeric, we want to support date objects too
        if (!is_numeric($min) || !is_numeric($max)) {
            $this->is_numeric = false;
        }
    }

    public function getMin($keep_int_limit = true)
    {
        if (!$keep_int_limit && $this->min === 0 && $this->is_numeric) {
            return null;
        }
        return $this->min;
    }

    public function getMax($keep_int_limit = true)
    {
        if (!$keep_int_limit && $this->max === PHP_INT_MAX && $this->is_numeric) {
            return null;
        }
        return $this->max;
    }
}