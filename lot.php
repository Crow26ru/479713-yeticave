<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

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
    print('Ошибка соединения: ' . mysqli_connect_error());
} else {
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


        if($lot) {
            $lot = mysqli_fetch_all($lot, MYSQLI_ASSOC);
            $rates_history = mysqli_fetch_all($rates_history, MYSQLI_ASSOC);
            $max_rate = mysqli_fetch_all($max_rate, MYSQLI_ASSOC);
            $date_end = mysqli_fetch_all($date_end, MYSQLI_ASSOC);

            if($lot) {

                // Преобразование двумерного ассоциативного массива из одного элемента в ассоциативный массив лота
                if(isset($lot[0])){
                    $lot = $lot[0];
                }

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

                // Установка флага сокрытия блока добавления ставки
                $is_hidden_rate = false;
                // Попробуем вытащить ID пользователя
                if($is_auth === 0) {
                    $is_hidden_rate = true;
                } else {
                    $email = $_SESSION['email'];

                    $user_id = get_id_user_db($con, $email);
                    $author_id = $lot['author'];

                    // Ищем ставки
                    $stmt = mysqli_prepare($con, FIND_RATE);
                    mysqli_stmt_bind_param($stmt, 'ss', $lot_id, $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    // Если результат запроса не NULL, то пользователь ранее добавлял ставку
                    $is_hidden_rate = isset($result[0]['id']) ? true : false;
                    $is_hidden_rate = $user_id === $author_id ? true : false;
                }

                // Подключаем шаблоны
                $categories_content = include_template('categories.php', ['categories'  => get_categories_list($con)]);

                $rates_content = include_template('history-rates.php', ['rates' => $rates_history]);

                $main_content = include_template('lot.php', [
                                                                 'categories_list' => $categories_content,
                                                                 'lot'             => $lot,
                                                                 'rates'           => $rates_content,
                                                                 'total_rate'      => $max_rate,
                                                                 'is_hidden_rate'  => $is_hidden_rate,
                                                                 'is_end'          => $is_end,
                                                                 'error'           => $error,
                                                                 'lot_id'          => $lot_id
                                                            ]);
                $all_content = include_template('layout.php', [
                                                                  'content'        => $main_content,
                                                                  'categories'     => get_categories_list($con),
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
            $cost = intval($_POST['cost']);

            // Прочитать из БД информацию о стартовой ставке и шаге
            // Прочитать из БД информацию о ставках на этот лот
            // Если введеный шаг ставки <= минимального шага, то показать ошибку, иначе:
            // Если до этого ставок не было, то в новую ставку записываем стартовую ставку + шаг, иначе:
            // В новую ставку записываем сумму последней ставки с введенным шагом

            $stmt = mysqli_prepare($con, LOT);
            mysqli_stmt_bind_param($stmt, 's', $lot_id);
            mysqli_stmt_execute($stmt);
            $lot = mysqli_stmt_get_result($stmt);
            $lot = mysqli_fetch_all($lot, MYSQLI_ASSOC);
            $start_rate = intval($lot[0]['start_rate']);
            $step = intval($lot[0]['step']);

            if($cost < $step) {
                $error_code = 3;
            } else {
                // Получение последней ставки
                $stmt = mysqli_prepare($con, RATES_HISTORY);
                mysqli_stmt_bind_param($stmt, 's', $lot_id);
                mysqli_stmt_execute($stmt);
                $last_rate = mysqli_stmt_get_result($stmt);
                $last_rate = mysqli_fetch_all($last_rate, MYSQLI_ASSOC);

                if(!isset($last_rate[0]['rate'])) {
                    $last_rate = $start_rate + $cost;
                } else {
                    $last_rate = intval($last_rate[0]['rate']) + $cost;
                }

                $email = $_SESSION['email'];

                // Выполняем запрос на получение ID
                $user_id = get_id_user_db($con, $email);

                // Выполняем запрос на добавление ставки
                $stmt = mysqli_prepare($con, ADD_RATE);
                mysqli_stmt_bind_param($stmt, 'iss', $last_rate, $user_id, $lot_id);
                $is_add = mysqli_stmt_execute($stmt);

                $error_code = 0;
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

        $categories_content = include_template('categories.php', ['categories'      => get_categories_list($con)]);

        $fail_content = include_template('404.php',              [
                                                                  'categories_list' => $categories_content,
                                                                  'title'           => $error_title,
                                                                  'message'         => $error_message
        ]);

        $all_content = include_template('layout.php',            [
                                                                  'content'         => $fail_content,
                                                                  'categories'      => get_categories_list($con),
                                                              'user_name'       => $user_name,
                                                              'is_auth'         => $is_auth,
                                                                  'page_name'       => $page_name
        ]);
        print($all_content);
    }
}
