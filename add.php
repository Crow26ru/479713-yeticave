<?php
// Список разрешенных MIME файлов
define('PERMIT_MIME_TYPES', ['image/pjpeg', 'image/jpeg', 'image/png']);

require_once('functions.php');
require_once('connect.php');

$categories = [];
$is_auth = rand(0, 1);
$is_good = false;
$user_name = 'Семён';
$page_name = 'Добавление лота - YetiCave';

// Запрос списока категорий
define(
    'CATEGORIES_LIST',
    'SELECT name AS categories FROM categories;'
);

//Получаем список категорий из БД
$rows_categories = mysqli_query($con, CATEGORIES_LIST);
$rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

//Разбор двумерного массива категорий из БД в одномерный масссив категорий
foreach($rows_categories as $category) {
    array_push($categories, $category['categories']);
}

$errors = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    $errors = [];

    // Проверяем были ли заполнены поля (все поля должны быть заполнены)
    foreach($lot as $key => $value) {
        if(empty($lot[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }

        if($key === 'category' && $value === 'Выберите категорию') {
            $errors[$key] = 'Выберите категорию';
        }
    }

    // Проверяем поля начальной ставки и шага ставки, что у них числовое значение
    foreach($lot as $key => $value) {
        if($key === 'lot-rate' || $key === 'lot-step') {
            if(!filter_var($value, FILTER_VALIDATE_INT)) {
                $errors[$key] = 'Введенное значение не является целым числом.';
            } else {
                if($value <= 0) {
                    $errors[$key] = 'Введенное значение должно быть положительным числом.';
                }
            }
        }
    }

    // Проверяем дату
    $date_regexp = '/\d{4}-\d{2}-\d{2}/';
    if (!preg_match($date_regexp, $lot['lot-date'])) {
        $errors['lot-date'] = 'Введите дату окончания лота в формате ДД-ММ-ГГГГ';
    } else {
        if(!strtotime($lot['lot-date'])) {
            $errors['lot-date'] = 'Указана неверная дата';
        }
    }

    // Проверяем был ли загружен файл
    if($_FILES['image']['name']) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = $_FILES['image']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        // Является ли тип файла разрешенным изображением?
        if(!array_search($file_type, PERMIT_MIME_TYPES)) {
            $errors['image'] = 'Загрузите картинку лота в формате PNG или JPEG';
        }

        // Если изображение валидно, то переместим его из временной директории
        if(!isset($errors['image'])) {
            move_uploaded_file($tmp_name, 'img/' . $path);
            $lot['image'] = $path;
        }
    } else {
        $errors['image'] = 'Вы не загрузили файл';
    }

    if(!$errors) {
        $errors = false;
    }
}

$categories_list = include_template('categories.php', ['categories' => $categories]);
$add_lot = include_template('add-lot.php', [
                                               'categories_list' => $categories_list,
                                               'categories'      => $categories,
                                               'errors'          => $errors
                                           ]);
$page = include_template('layout.php', [
                                            'content'        => $add_lot,
                                            'categories'     => $categories,
                                            'user_name'      => $user_name,
                                            'is_auth'        => $is_auth,
                                            'page_name'      => $page_name
]);

print($page);
