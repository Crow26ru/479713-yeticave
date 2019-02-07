<?php
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function get_time_of_end_lot() {
    date_default_timezone_set('Europe/Moscow');

    $current_time = time();
    $time_live_a_lot = strtotime('tomorrow midnight');

    $time_live_a_lot = $time_live_a_lot - $current_time;
    $hours = floor($time_live_a_lot / 3600);
    $minutes = floor($time_live_a_lot % 3600 / 60);

    if ($hours < 10) {
        $hours = '0' . $hours;
    }

    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    
    $time_live_a_lot = $hours . ':' . $minutes;
    print($time_live_a_lot);
}
