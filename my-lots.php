<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');
require_once('update-db.php');

$page_name = 'Мои лоты - YetiCave';

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $email =  $_SESSION['email'];
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
} else {
    // 1 - Получить ID пользователя из сессии
    // 2 - Выполнить запрос к таблице сделанных ставок и получить результат
    // 3 - К полученным данным добавить флаг, что время окончания торгов близится к концу
    // 4 - Отправить данные в шаблон и вывести его

    if(!$email) {
        http_response_code(403);
        $error_title = 'Ошибка 403: Доступ к странице закрыт';
        $error_message = 'Эта страница доступна только для зарегистрированных пользователей.';
        $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
        print($page);
    } else {
        $user_id = select_stmt_query($con, FIND_USER, [$email]);
        $user_id = $user_id[0]['id'];
        $user_rates = select_stmt_query($con, USER_RATES, [$user_id]);

        if(empty($user_rates)) {
            $error_title = 'Ставок нет';
            $error_message = 'Вы ещё не делали ставки по выставленным лотам.';
            $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
            print($page);
        } else {
            foreach($user_rates as $key => $rate) {
                $is_finishing = false;
                $is_end = false;
                $is_win = $user_id === intval($rate['winner_id']) ? true : false;
                $time_end = strtotime($rate['time']) - time();

                if($time_end <= 3600 && $time_end > 0) {
                    $is_finishing = true;
                } else if($time_end <= 0) {
                    $is_end = true;
                }

                $user_rates[$key]['is_finishing'] = $is_finishing;
                $user_rates[$key]['is_end'] = $is_end;
                $user_rates[$key]['is_win'] = $is_win;
            }

            $categories_list = include_template('categories.php', ['categories' => get_categories_db($con)]);
            $page_content = include_template('my-lots.php', [
                'categories_list' => $categories_list,
                'rates'           => $user_rates
            ]);
            $page = include_template('layout.php', [
                'content'        => $page_content,
                'categories'     => get_categories_db($con),
                'user_name'      => $user_name,
                'is_auth'        => $is_auth,
                'page_name'      => $page_name
            ]);

        print($page);
        }
    }
}
