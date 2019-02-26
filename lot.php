<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

$categories = [];
$is_good = false;
$page_name = 'Аукцион - YetiCave';

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $is_auth = 1;
} else {
    $user_name = '';
    $is_auth = 0;
}

$rows_categories = mysqli_query($con, CATEGORIES_LIST);
$rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

// Преобразование двумерного ассоциативного массива в простой массив категорий
foreach($rows_categories as $value) {
    array_push($categories, $value['categories']);
}

// Проверяем существует ли параметр id, переданный в запросе GET
if(isset($_GET['id'])) {
    $lot_id = $_GET['id'];

    // Выполняем запросы
    $stmt = mysqli_prepare($con, LOT);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $lot = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($con, RATES_HISTORY);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $rates_history = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($con, LAST_RATE);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $max_rate = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($con, DATE_END);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $date_end = mysqli_stmt_get_result($stmt);


    if ($lot) {
        $lot = mysqli_fetch_all($lot, MYSQLI_ASSOC);
        $rates_history = mysqli_fetch_all($rates_history, MYSQLI_ASSOC);
        $max_rate = mysqli_fetch_all($max_rate, MYSQLI_ASSOC);
        $date_end = mysqli_fetch_all($date_end, MYSQLI_ASSOC);

        if ($lot) {
            // Преобразование двумерного ассоциативного массива из одного элемента в ассоциативный массив лота
            if (isset($lot[0])){
                $lot = $lot[0];
            }

            if (empty($max_rate[0]['max_rate'])) {
                $max_rate = $lot['start_rate'];
            } else {
                $max_rate = $max_rate[0]['max_rate'];
            }

            // Переводим полученную дату окончания торгов по лоту в UNIX time
            $date_end = strtotime($date_end[0]['date_end']);

            // Установка флага завершения торгов по лоту
            $is_end = ($date_end <= time()) ? true : false;

            // Записываем значение минимальной ставки
            $lot['min_cost'] = $lot['step'] + $max_rate;

            // Проверяем были ли ошибки при выполнении ставки
            $error = '';
            if(isset($_GET['error_code'])) {
                $error_code = $_GET['error_code'];

                switch($error_code) {
                    case 1:
                        $error = 'Заполните это поле';
                        break;
                    case 2:
                        $error = 'Ставка должна быть целым числом';
                        break;
                    case 3:
                        $error = 'Ваша ставка слишком низкая';
                        break;
                }
            }

            // Подключаем шаблоны
            $categories_content = include_template('categories.php', ['categories'  => $categories]);

            $rates_content = include_template('history-rates.php', ['rates' => $rates_history]);

            $main_content = include_template('lot.php', [
                                                             'categories_list' => $categories_content,
                                                             'is_auth'         => $is_auth,
                                                             'lot'             => $lot,
                                                             'rates'           => $rates_content,
                                                             'total_rate'      => $max_rate,
                                                             'is_end'          => $is_end,
                                                             'error'           => $error,
                                                             'lot_id'          => $lot_id
                                                        ]);
            $all_content = include_template('layout.php', [
                                                              'content'        => $main_content,
                                                              'categories'     => $categories,
                                                              'user_name'      => $user_name,
                                                              'is_auth'        => $is_auth,
                                                              'page_name'      => $page_name
                                                          ]);

            print($all_content);
            $is_good = true;
        }
    }
}

if(isset($_POST['id'])) {
    /*
      Коды ошибок ставки:
      0 - Нет ошибки;
      1 - Не указана ставка;
      2 - Введено не численное значение;
      3 - Ставка слишком низкая
    */
    $is_good = true;

    $lot_id = $_POST['id'];

    if(!$_POST['cost']) {
        $error_code = 1;
    } else if(!filter_var($_POST['cost'], FILTER_VALIDATE_INT)) {
        $error_code = 2;
    } else {
        $cost = $_POST['cost'];

        // Получение шага ставки
        $stmt = mysqli_prepare($con, STEP_RATE);
        mysqli_stmt_bind_param($stmt, 's', $lot_id);
        mysqli_stmt_execute($stmt);
        $step = mysqli_stmt_get_result($stmt);
        $step = mysqli_fetch_all($step, MYSQLI_ASSOC);

        // Получение последней ставки
        $stmt = mysqli_prepare($con, RATES_HISTORY);
        mysqli_stmt_bind_param($stmt, 's', $lot_id);
        mysqli_stmt_execute($stmt);
        $last_rate = mysqli_stmt_get_result($stmt);
        $last_rate = mysqli_fetch_all($last_rate, MYSQLI_ASSOC);

        // Если до этого ставок не было, то надо записать начальную ставку
        if(!$last_rate) {
            $stmt = mysqli_prepare($con, LOT);
            mysqli_stmt_bind_param($stmt, 's', $lot_id);
            mysqli_stmt_execute($stmt);
            $last_rate = mysqli_stmt_get_result($stmt);
            $last_rate = mysqli_fetch_all($last_rate, MYSQLI_ASSOC);
            $last_rate = $last_rate[0]['start_rate'];
        }

        $step = $step[0]['step_value'];
        $last_rate = $last_rate[0]['rate'];

        // Указываем минимальную ставку
        $min_rate = intval($last_rate) + intval($step);

        // Если минимальная ставка меньше или равна указанной ставке, то выполнить запросы и вернуть код ошибки 0
        // Иначе вернем код ошибки 3
        if($min_rate <= $cost) {
            // Добавим ставку в таблицу rates
            // Для добавления ставки нужно узнать ID пользователя
            // Это можно сделать с помощью e-mail, который хранится в сессии
            $email = $_SESSION['email'];

            // Выполняем запрос на получение ID
            $stmt = mysqli_prepare($con, FIND_USER);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $user_id = mysqli_stmt_get_result($stmt);
            $user_id = mysqli_fetch_all($user_id, MYSQLI_ASSOC);
            $user_id = $user_id[0]['id'];

            // Выполняем запрос на добавление ставки
            $stmt = mysqli_prepare($con, ADD_RATE);
            mysqli_stmt_bind_param($stmt, 'iss', $cost, $user_id, $lot_id);
            $is_add = mysqli_stmt_execute($stmt);
            $error_code = 0;
        } else {
            $error_code = 3;
        }
    }

    if($error_code !== 0) {
        header('Location: ./lot.php?id=' . $lot_id . '&error_code=' . $error_code);
    } else {
        header('Location: ./lot.php?id=' . $lot_id);
    }
}

if(!$is_good) {
    http_response_code(404);
    $error_title = 'Ошибка 404: Страница не найдена';
    $error_message = 'Данной страницы не существует на сайте.';

    $categories_content = include_template('categories.php', ['categories'      => $categories]);

    $fail_content = include_template('404.php',              [
                                                              'categories_list' => $categories_content,
                                                              'title'           => $error_title,
                                                              'message'         => $error_message
    ]);

    $all_content = include_template('layout.php',            [
                                                              'content'         => $fail_content,
                                                              'categories'      => $categories,
                                                              'user_name'       => $user_name,
                                                              'is_auth'         => $is_auth,
                                                              'page_name'       => $page_name
                                                             ]);
    print($all_content);
}
