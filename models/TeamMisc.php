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
];

// $team_stat_column_data = [
//     //contains the display name and the formatter function (if null, regular number formatting is used)
//     'clears' => ['Clears', null],
//     'total_ss' => ['Total SS', null],
//     'total_s' => ['Total S', null],
//     'total_a' => ['Total A', null],
//     'play_count' => ['Play Count', null],
//     'play_time' => [
//         'Play Time',
//         function ($value) {
//             //convert second count to readable time format
//             return seconds2human($value);
//         },
//         true
//     ],
//     'ranked_score' => ['Ranked Score', function ($value) {
//         return shorten_number($value);
//     }, true],
//     'total_score' => ['Total Score', function ($value) {
//         return shorten_number($value);
//     }, true],
//     'average_score' => ['Average Score', function ($value) {
//         return number_format($value, 0);
//     }, true],
//     'total_hits' => ['Total Hits', null],
//     'replays_watched' => ['Replays Watched', null],
//     'performance' => [
//         'Performance',
//         function ($value) {
//             return number_format($value, 0) . 'pp';
//         }
//     ],
// ];
$team_stat_column_data = [
    'clears' => [
        'name' => 'Clears',
        'formatter' => null,
        'tooltip' => false
    ],
    'total_ss' => [
        'name' => 'Total SS',
        'formatter' => null,
        'tooltip' => false
    ],
    'total_s' => [
        'name' => 'Total S',
        'formatter' => null,
        'tooltip' => false
    ],
    'total_a' => [
        'name' => 'Total A',
        'formatter' => null,
        'tooltip' => false
    ],
    'play_count' => [
        'name' => 'Play Count',
        'formatter' => null,
        'tooltip' => false
    ],
    'play_time' => [
        'name' => 'Play Time',
        'formatter' => function ($value) {
            return seconds2human($value);
        },
        'tooltip' => false
    ],
    'ranked_score' => [
        'name' => 'Ranked Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        }
    ],
    'total_score' => [
        'name' => 'Total Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        }
    ],
    'average_score' => [
        'name' => 'Average Score',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        }
    ],
    'total_hits' => [
        'name' => 'Total Hits',
        'formatter' => function ($value) {
            return shorten_number($value);
        },
        'tooltip' => function ($value) {
            return number_format($value, 0);
        }
    ],
    'replays_watched' => [
        'name' => 'Replays Watched',
        'formatter' => null,
        'tooltip' => false
    ],
    'performance' => [
        'name' => 'Performance',
        'formatter' => function ($value) {
            return number_format($value, 0) . 'pp';
        },
        'tooltip' => false
    ],
];

function get_team_row_data($team)
{
    global $team_stat_column_data;
    $data = [];
    foreach ($team_stat_column_data as $key => $value) {
        $formatter = $value['formatter'] ?? 'number_format';
        $formatter_tooltip = $value['tooltip'];
        $val = $team->getRuleset()->get($key);
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