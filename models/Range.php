<?php

class Range
{
    public $min;
    public $max;
    public $is_numeric = true;
    public $manual_type = null;

    public function __construct($min, $max, $manual_type = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->manual_type = $manual_type;

        // check if min and max are numeric, we want to support date objects too
        if (!is_numeric($min) || !is_numeric($max)) {
            $this->is_numeric = false;
        }
    }

    public function setMin($min)
    {
        $this->min = $min;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function getMin($keep_int_limit = true)
    {
        if (!$keep_int_limit && ($this->min === 0 || $this->min === '1970-01-01')) {
            return null;
        }
        return $this->min;
    }

    public function getMax($keep_int_limit = true)
    {
        if (!$keep_int_limit && ($this->max === PHP_INT_MAX || $this->max === '9999-01-01')) {
            return null;
        }
        return $this->max;
    }

    public function getDefaultMin($keep_int_limit = true){
        //return min if it exists and is not null
        if ($this->min !== null && $this->min !== 0 && $this->min !== '') {
            return $this->min;
        }

        //get the type of the min value
        $type = $manual_type ?? gettype($this->min);

        //just check for numbers and date for now
        switch($type) {
            case 'integer':
            case 'double':
                return 0;
            case 'date':
                return '1970-01-01';
                break;
            default:
                return null;
        }
    }

    public function getDefaultMax($keep_int_limit = true){
        //return max if it exists and is not null
        if ($this->max !== null && $this->max !== PHP_INT_MAX && $this->max !== '') {
            return $this->max;
        }

        //get the type of the max value
        $type = $manual_type ?? gettype($this->max);

        //just check for numbers and date for now
        switch($type) {
            case 'integer':
            case 'double':
                return PHP_INT_MAX;
            case 'date':
                return '9999-12-31';
                break;
            default:
                return null;
        }
    }
}