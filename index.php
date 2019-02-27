<?php
$is_auth = 0;
$page_name = 'Главная - YetiCave';

require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

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
    $res_lots = mysqli_query($con, NEW_LOTS_LIST);
    $rows_lots = mysqli_fetch_all($res_lots, MYSQLI_ASSOC);

    // Подключение шаблонов
    $page_content = include_template('index.php',   [
                                                  'categories'    => get_categories_list($con),
                                                  'products'      => $rows_lots
                                                ]);
    $layout_content = include_template('layout.php', [
                                                   'content'    => $page_content,
                                                   'user_name'  => $user_name,
                                                   'is_auth'    => $is_auth,
                                                   'page_name'  => $page_name,
                                                   'categories' => get_categories_list($con)
                                                 ]);

    // Отправка сформированной разметки из шаблонов
    print($layout_content);
}
