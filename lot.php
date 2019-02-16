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

$categories = [];
$is_auth = rand(0, 1);
$user_name = 'Семён';
$page_name = 'Аукцион - YetiCave';

require_once('functions.php');
require_once('connect.php');

// Проверяем существует ли параметр id, переданный в запросе GET
if(isset($_GET['id'])) {
    $lot_id = $_GET['id'];

    // Выполняем запросы
    $stmt = mysqli_prepare($con, LOT);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $lot = mysqli_stmt_get_result($stmt);
    
    $rows_categories = mysqli_query($con, CATEGORIES_LIST);
    
    $stmt = mysqli_prepare($con, RATES_HISTORY);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $rates_history = mysqli_stmt_get_result($stmt);
    
    $stmt = mysqli_prepare($con, RATES_TOTAL);
    mysqli_stmt_bind_param($stmt, 'i', $lot_id);
    mysqli_stmt_execute($stmt);
    $rates_total = mysqli_stmt_get_result($stmt);

    if ($lot && $rows_categories) {
        $lot = mysqli_fetch_all($lot, MYSQLI_ASSOC);
        $rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);
        $rates_history = mysqli_fetch_all($rates_history, MYSQLI_ASSOC);
        $rates_total = mysqli_fetch_all($rates_total, MYSQLI_ASSOC);
        
        if ($lot && $rows_categories) {
            // Преобразование двумерного ассоциативного массива в простой массив категорий
            foreach($rows_categories as $value) {
                array_push($categories, $value['categories']);
            }
        
            // Преобразование двумерного ассоциативного массива из одного элемента в ассоциативный массив лота
            if (isset($lot[0])){
                $lot = $lot[0];
            }
            
            if (isset($rates_total[0]['total'])) {
                $rates_total = intval($rates_total[0]['total'], 10);
            }
        
            // Подключаем шаблоны
            $rates_content = include_template('history-rates.php', [
                                                                       'rates' => $rates_history
                                                                   ]);
            
            $main_content = include_template('lot.php', [
                                                             'categories'  => $categories,
                                                             'lot'         => $lot,
                                                             'rates'       => $rates_content,
                                                             'total_rate'  => $rates_total
                                                        ]);
            $all_content = include_template('layout.php', [
                                                              'content'    => $main_content,
                                                              'categories' => $categories,
                                                              'user_name'  => $user_name,
                                                              'is_auth'    => $is_auth,
                                                              'page_name'  => $page_name
                                                          ]);
        
            print($all_content);
        } else {
            http_response_code(404);
        }
    } else {
        http_response_code(500);
    }
} else {
    http_response_code(404);
}
