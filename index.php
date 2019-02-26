<?php

// Константы SQL запросов
define(
    'CATEGORIES_LIST',
    'SELECT name AS categories FROM categories;'
);
define(
    'NEW_LOTS_LIST',
    'SELECT
        lots.id,
        lots.name,
        categories.name AS category,
        lots.start_rate AS price,
        lots.image,
        lots.date_end AS time
     FROM lots
     JOIN categories ON lots.category_id = categories.id
     WHERE date_end > NOW()
     ORDER BY date_add DESC
     LIMIT 9;'
);

$is_auth = 0;
$categories = [];
$page_name = 'Главная - YetiCave';

require('functions.php');
require('connect.php');

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $is_auth = 1;
} else {
    $user_name = '';
}
// Выполнение запросов к БД
if(!$con) {
    print('Ошибка соединения: ' . mysqli_connect_error());
} else {
    $res_categories = mysqli_query($con, CATEGORIES_LIST);
    $rows_categories = mysqli_fetch_all($res_categories, MYSQLI_ASSOC);

    $res_lots = mysqli_query($con, NEW_LOTS_LIST);
    $rows_lots = mysqli_fetch_all($res_lots, MYSQLI_ASSOC);
}

// Преобразование двумерного ассоциативного массива в простой массив категорий
foreach($rows_categories as $value) {
    array_push($categories, $value['categories']);
}

$categories_length = count($categories);
$products = $rows_lots;

// Подключение шаблонов
$page_content = include_template('index.php',   [
                                                  'categories'    => $categories,
                                                  'products'      => $products
                                                ]);
$layout_content = include_template('layout.php', [
                                                   'content'    => $page_content,
                                                   'user_name'  => $user_name,
                                                   'is_auth'    => $is_auth,
                                                   'page_name'  => $page_name,
                                                   'categories' => $categories
                                                 ]);

// Отправка сформированной разметки из шаблонов
print($layout_content);
