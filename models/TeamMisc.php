<?php
$team_order_options = [
    // 'id' => 'ID',
    // 'members' => 'Members',
    // 'clears' => 'Clears',
    // 'total_ss' => 'Total SS',
    // 'total_s' => 'Total S',
    // 'total_a' => 'Total A',
    // 'play_count' => 'Play Count',
    // 'play_time' => 'Play Time',
    // 'ranked_score' => 'Ranked Score',
    // 'total_score' => 'Total Score',
    // 'average_score' => 'Average Score',
    // 'performance' => 'Performance',
    // 'replays_watched' => 'Replays Watched',
    // 'total_hits' => 'Total Hits',
    'id' => [ 'name' => 'ID', 'can_hide' => false ],
    'members' => [ 'name' => 'Members', 'can_hide' => false ],
    'clears' => [ 'name' => 'Clears', 'can_hide' => true ],
    'total_ss' => [ 'name' => 'Total SS', 'can_hide' => true ],
    'total_s' => [ 'name' => 'Total S', 'can_hide' => true ],
    'total_a' => [ 'name' => 'Total A', 'can_hide' => true ],
    'play_count' => [ 'name' => 'Play Count', 'can_hide' => true ],
    'play_time' => [ 'name' => 'Play Time', 'can_hide' => true ],
    'ranked_score' => [ 'name' => 'Ranked Score', 'can_hide' => true ],
    'total_score' => [ 'name' => 'Total Score', 'can_hide' => true ],
    'average_score' => [ 'name' => 'Average Score', 'can_hide' => true ],
    'performance' => [ 'name' => 'Performance', 'can_hide' => true ],
    'replays_watched' => [ 'name' => 'Replays Watched', 'can_hide' => true ],
    'total_hits' => [ 'name' => 'Total Hits', 'can_hide' => true ],
    'created_at' => [ 'name' => 'Formed', 'can_hide' => true, 'type' => 'date' ],
];

$team_stat_column_data = [
    'clears' => [
        'name' => 'Clears',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'total_ss' => [
        'name' => 'Total SS',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'total_s' => [
        'name' => 'Total S',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'total_a' => [
        'name' => 'Total A',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'play_count' => [
        'name' => 'Play Count',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'play_time' => [
        'name' => 'Play Time',
        'formatter' => function ($value) {
            return seconds2human($value);
        },
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'ranked_score' => [
        'name' => 'Ranked Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        },
        'is_ruleset_value' => true
    ],
    'total_score' => [
        'name' => 'Total Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        },
        'is_ruleset_value' => true
    ],
    'average_score' => [
        'name' => 'Average Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        },
        'is_ruleset_value' => true
    ],
    'total_hits' => [
        'name' => 'Total Hits',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        },
        'is_ruleset_value' => true
    ],
    'replays_watched' => [
        'name' => 'Replays Watched',
        'formatter' => null,
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'performance' => [
        'name' => 'Performance',
        'formatter' => function ($value) {
            return number_format($value, 0) . 'pp';
        },
        'tooltip' => false,
        'is_ruleset_value' => true
    ],
    'created_at' => [
        'name' => 'Formed',
        'formatter' => function ($value) {
            return getTeamDateString($value);
        },
        'tooltip' => false,
        'is_ruleset_value' => false
    ],
];

function getTeamDateString($str){
    $format = 'Y-m-d H:i:s';
    $dateTime = DateTime::createFromFormat($format, $str);
    return $dateTime->format('Y-m-d H:i:s');
}

function get_team_row_data($team)
{
    global $team_stat_column_data;
    $data = [];
    foreach ($team_stat_column_data as $key => $value) {
        $formatter = $value['formatter'] ?? 'number_format';
        $formatter_tooltip = $value['tooltip'];
        $val = $value['is_ruleset_value'] ? $team->getRuleset()->get($key) : $team->get($key);
        $data[$key] = [
            'value' => $val,
            'tooltip' => null
        ];
        if ($formatter_tooltip && $val !== null) {
            $data[$key]['tooltip'] = $formatter_tooltip($val);
        }
        if ($val === null) {
            $data[$key]['value'] = '-';
        } else {
            $data[$key]['value'] = $formatter($val);
        }
    }
    return $data;
}

function shorten_number($n, $precision = 1)
{
    //we have values in the trillions,
    //shorten M, B, T
    if ($n < 1000000) {
        return number_format($n);
    }
    if ($n < 1000000000) {
        return number_format($n / 1000000, $precision) . 'M';
    }
    if ($n < 1000000000000) {
        return number_format($n / 1000000000, $precision) . 'B';
    }
    return number_format($n / 1000000000000, $precision) . 'T';
}

function seconds2human($ss)
{
    //convert second count to readable time format
    //only show the two biggest units
    //month, day, hour
    $units = [
        'y' => 365 * 24 * 60 * 60,
        'm' => 30 * 24 * 60 * 60,
        'd' => 24 * 60 * 60,
    ];
    $human = '';
    foreach ($units as $unit => $value) {
        if ($ss < $value) {
            continue;
        }
        $amount = floor($ss / $value);
        $ss -= $amount * $value;
        $human .= $amount . $unit . ' ';
    }
    return $human;
}