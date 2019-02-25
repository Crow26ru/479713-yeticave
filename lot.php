<?php
// 1 - Получить ID лота в параметре GET
// 2 - Проверить, что это значение существует в массиве GET
// 3 - Выполнить запрос к БД для получения информации о лоте
// 4 - Подключить шаблоны
// 5 - Отправить пользователю сформированную разметку

// Константы SQL запросов
// Запрос списка лотов
define(
    'LOT',
    'SELECT lots.name,
        lots.description,
        lots.image,
        lots.start_rate AS start_rate,
        lots.date_end AS time,
        lots.step_value AS step,
        categories.name AS category
    FROM lots
    JOIN categories ON lots.category_id = categories.id
    WHERE lots.id = ?;'
);

// Запрос списока категорий
define(
    'CATEGORIES_LIST',
    'SELECT name AS categories FROM categories;'
);

// Запрос истории ставок
define(
    'RATES_HISTORY',
    'SELECT
        users.name,
        rates.rate,
        rates.date_add AS time
    FROM rates
    JOIN users ON users.id = rates.user_id
    WHERE rates.lot_id = ?
    ORDER BY rates.date_add DESC;'
);

// Запрос суммы ставок
define(
    'RATES_TOTAL',
    'SELECT sum(rates.rate) AS total
    FROM rates
    JOIN users ON users.id = rates.user_id
    WHERE rates.lot_id = ?;'
);

// Запрос даты окончания лота
define(
    'DATE_END',
    'SELECT date_end FROM lots WHERE id = ?;'
);

$categories = [];
$is_good = false;
$page_name = 'Аукцион - YetiCave';

require_once('functions.php');
require_once('connect.php');

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
    
    $stmt = mysqli_prepare($con, RATES_TOTAL);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $rates_total = mysqli_stmt_get_result($stmt);
    
    $stmt = mysqli_prepare($con, DATE_END);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $date_end = mysqli_stmt_get_result($stmt);
    

    if ($lot) {
        $lot = mysqli_fetch_all($lot, MYSQLI_ASSOC);
        $rates_history = mysqli_fetch_all($rates_history, MYSQLI_ASSOC);
        $rates_total = mysqli_fetch_all($rates_total, MYSQLI_ASSOC);
        $date_end = mysqli_fetch_all($date_end, MYSQLI_ASSOC);
        
        if ($lot) {    
            // Преобразование двумерного ассоциативного массива из одного элемента в ассоциативный массив лота
            if (isset($lot[0])){
                $lot = $lot[0];
            }
            
            if (empty($rates_total[0]['total'])) {
                $rates_total = $lot['start_rate'];
            } else {
                $rates_total = $lot['start_rate'] + $rates_total[0]['total'];
            }

            // Переводим полученную дату окончания торгов по лоту в UNIX time
            $date_end = strtotime($date_end[0]['date_end']);
            
            // Установка флага завершения торгов по лоту
            $is_end = ($date_end <= time()) ? true : false;

            // Подключаем шаблоны
            $categories_content = include_template('categories.php', ['categories'  => $categories]);
            
            $rates_content = include_template('history-rates.php', ['rates' => $rates_history]);
            
            $main_content = include_template('lot.php', [
                                                             'categories_list' => $categories_content,
                                                             'is_auth'         => $is_auth,
                                                             'lot'             => $lot,
                                                             'rates'           => $rates_content,
                                                             'total_rate'      => $rates_total,
                                                             'is_end'          => $is_end
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