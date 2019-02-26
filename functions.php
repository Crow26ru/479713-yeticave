<?php
define('HUMAN_MINUTES', ' минут назад');
define('HUMAN_HOURS', ' часов назад');

date_default_timezone_set('Europe/Moscow');
session_start();

// Функция для форматирования суммы
function show_price($price) {
    $price = ceil($price);
    $price = number_format($price, 0, '', ' ');
    return $price . ' &#8381;';
}

// Функция шаблонизатор
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

// Отображение окончания лота в формате ЧЧ:ММ
function get_time_of_end_lot($time, $is_hh_mm_ss_format = false) {
    $current_time = time();
    $time_lives_a_lot = strtotime($time);

    $time_lives_a_lot = $time_lives_a_lot - $current_time;
    $hours = floor($time_lives_a_lot / 3600);
    $minutes = floor($time_lives_a_lot % 3600 / 60);

    if($hours < 10) {
        $hours = '0' . $hours;
    }

    if($minutes < 10) {
        $minutes = '0' . $minutes;
    }

    if($is_hh_mm_ss_format) {
        $seconds = $time_lives_a_lot - ($hours * 3600) - ($minutes * 60);

        if($seconds < 10) {
            $seconds = '0' . $seconds;
            $time_lives_a_lot = $hours . ':' . $minutes . ':' . $seconds;
            return $time_lives_a_lot;
        }

        $time_lives_a_lot = $hours . ':' . $minutes . ':' . $seconds;
        return $time_lives_a_lot;
    }

    $time_lives_a_lot = $hours . ':' . $minutes;
    return $time_lives_a_lot;
}

// Отображение времени в удобном виде
function show_user_frendly_time($time) {
    $current_time = time();
    $time_lives_a_lot = strtotime($time);


    $time_lives_a_lot = $current_time - $time_lives_a_lot;
    $hours = floor($time_lives_a_lot / 3600);
    $minutes = floor($time_lives_a_lot % 3600 / 60);

    if($hours == 0) {
        $time = $minutes . HUMAN_MINUTES;
        return $time;
    } elseif($hours > 0 && $hours < 24) {
        $time = $hours . HUMAN_HOURS;
        return $time;
    }

    $date = date('d.m.Y', strtotime($time));
    $time = date('H:i', strtotime($time));

    return $date . ' ' . $time;
}

// Работа с загружаемым изображением
function remove_image($path, $tmp_name) {
    // Разберем путь файла на составляющие
    $path = pathinfo($path);

    // Назначаем изображению уникальное имя
    $uniq_path = 'img/' . uniqid() . '.' . $path['extension'];

    // Перемещаем изображение из временной директории
    move_uploaded_file($tmp_name, $uniq_path);

    // Вернем название нового файла, чтобы верно прописать в БД путь к изображению
    return $uniq_path;
}
