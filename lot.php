<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');
require_once('update-db.php');

$is_good = false;
$page_name = 'Аукцион - YetiCave';

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $is_auth = 1;
} else {
    $user_name = '';
    $is_auth = 0;
}

if(!$con) {
    http_response_code(500);
    $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
    $error_message = 'Сайт временно недоступен. Попробуйте зайти позже';
    $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
    print($page);
    die();
}
// Проверяем существует ли параметр id, переданный в запросе GET
if(isset($_GET['id'])) {
    $lot_id = $_GET['id'];

    $lot = select_stmt_query($con, LOT, [$lot_id]);
    $rates_history = select_stmt_query($con, RATES_HISTORY, [$lot_id]);
    $max_rate = select_stmt_query($con, LAST_RATE, [$lot_id]);
    $date_end = select_stmt_query($con, DATE_END, [$lot_id]);

    if(!empty($lot)) {
        $lot = $lot[0];

        if(empty($max_rate[0]['max_rate'])) {
            $max_rate = $lot['start_rate'];
        } else {
            $max_rate = $max_rate[0]['max_rate'];
        }

        // Переводим полученную дату окончания торгов по лоту в UNIX time
        $date_end = strtotime($date_end[0]['date_end']);

        // Установка флага завершения торгов по лоту
        $is_end = ($date_end <= time()) ? true : false;

        // Проверяем были ли ошибки при выполнении ставки
        $error = '';

        if(isset($_SESSION['error_code'])) {
            $map_errors = ['1' => 'Заполните это поле', '2' => 'Ставка должна быть целым числом', '3' => 'Ваша ставка слишком низкая'];
            $error_code = intval($_SESSION['error_code']);

            foreach($map_errors as $key => $value) {
                if($key === $error_code) {
                    $error = $value;
                }
            }
            $_SESSION['error_code'] = '';
        }

        // Установка флага сокрытия блока добавления ставки
        $is_hidden_rate = false;
        // Попробуем вытащить ID пользователя
        if($is_auth === 0) {
            $is_hidden = true;
        } else {
            $email = $_SESSION['email'];

            $user_id = get_id_user_db($con, $email);
            $author_id = $lot['author'];

            // Ищем ставки
            $result = select_stmt_query($con, FIND_RATE, [$lot_id, $user_id]);

            // Если результат запроса не NULL, то пользователь ранее добавлял ставку
            $is_hidden_rate = isset($result[0]['id']) ? true : false;
            $is_hidden_author = $user_id === $author_id ? true : false;
                
            $is_hidden = $is_hidden_rate || $is_hidden_author;
        }

        // Подключаем шаблоны
        $categories_content = include_template('categories.php', ['categories'  => get_categories_db($con)]);

        $rates_content = include_template('history-rates.php', ['rates' => $rates_history]);

        $main_content = include_template('lot.php', [
            'categories_list' => $categories_content,
            'lot'             => $lot,
            'rates'           => $rates_content,
            'total_rate'      => $max_rate,
            'is_hidden'       => $is_hidden,
            'is_end'          => $is_end,
            'error'           => $error,
            'lot_id'          => $lot_id
        ]);

        $all_content = include_template('layout.php', [
            'content'        => $main_content,
            'categories'     => get_categories_db($con),
            'user_name'      => $user_name,
            'is_auth'        => $is_auth,
            'page_name'      => $page_name
        ]);

        print($all_content);
        $is_good = true;
        die();
    }
}


if(isset($_POST['id'])) {
    $is_good = true;

    $lot_id = $_POST['id'];

    if(!$_POST['cost']) {
        $error_code = 1;
        $_SESSION['error_code'] = $error_code;
        header('Location: ./lot.php?id=' . $lot_id);
        die();

    } else if(!filter_var($_POST['cost'], FILTER_VALIDATE_INT)) {
        $error_code = 2;
        $_SESSION['error_code'] = $error_code;
        header('Location: ./lot.php?id=' . $lot_id);
        die();
    }

    $cost = intval($_POST['cost']);

    // Прочитать из БД информацию о стартовой ставке и шаге
    // Прочитать из БД информацию о ставках на этот лот
    // Если введеный шаг ставки <= минимального шага, то показать ошибку, иначе:
    // Если до этого ставок не было, то в новую ставку записываем стартовую ставку + шаг, иначе:
    // В новую ставку записываем сумму последней ставки с введенным шагом

    $lot = select_stmt_query($con, LOT, [$lot_id]);
    $start_rate = intval($lot[0]['start_rate']);
    $step = intval($lot[0]['step']);

    if($cost < $step) {
        $error_code = 3;
        $_SESSION['error_code'] = $error_code;
        header('Location: ./lot.php?id=' . $lot_id);
        die();
    }

    // Получение последней ставки
    $last_rate = select_stmt_query($con, RATES_HISTORY, [$lot_id]);

    if(!isset($last_rate[0]['rate'])) {
        $last_rate = $start_rate + $cost;
    } else {
        $last_rate = intval($last_rate[0]['rate']) + $cost;
    }

    $email = $_SESSION['email'];

    // Выполняем запрос на получение ID
    $user_id = get_id_user_db($con, $email);

    // Выполняем запрос на добавление ставки
    $is_add = insert_stmt_query($con, ADD_RATE, [$last_rate, $user_id, $lot_id]);

    if($is_add) {
        $error_code = 0;
    } else {
        http_response_code(500);
        $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
        $error_message = 'Попробуйте добавить лот позже.';
        get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
        print($all_content);
        die();
    }

    header('Location: ./lot.php?id=' . $lot_id);
    die();
}


if(!$is_good) {
    http_response_code(404);
    $error_title = 'Ошибка 404: Страница не найдена';
    $error_message = 'Данной страницы не существует на сайте.';
    $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
    print($page);
}
