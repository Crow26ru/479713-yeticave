<?php
// Запрос списка категорий
define(
    'CATEGORIES_LIST',
    'SELECT name AS categories FROM categories;'
);

// Запрос для аутентификации
define(
    'USER_AUTH',
    'SELECT email, password
     FROM users
     WHERE email = ? AND password = ?;'
);

require_once('functions.php');
require_once('connect.php');

$categories = [];
$is_auth = rand(0, 1);
$user_name = 'Семён';
$page_name = 'Вход - YetiCave';


$rows_categories = mysqli_query($con, CATEGORIES_LIST);
$rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

// Преобразование двумерного ассоциативного массива в простой массив категорий
foreach($rows_categories as $value) {
    array_push($categories, $value['categories']);
}


$categories_content = include_template('categories.php', ['categories' => $categories]);
$login_content = include_template('login.php',  ['categories_list' => $categories_content]);
$page = include_template('layout.php', [
                                                  'content'        => $login_content,
                                                  'categories'     => $categories,
                                                  'user_name'      => $user_name,
                                                  'is_auth'        => $is_auth,
                                                  'page_name'      => $page_name
]);

print($page);