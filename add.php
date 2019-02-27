<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

$is_auth = 0;
$is_good = false;
$page_name = 'Добавление лота - YetiCave';
$errors = false;

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $is_auth = 1;
} else {
    $user_name = '';
}

if(!$con) {
    print('Ошибка соединения: ' . mysqli_connect_error());
} else {
    if(isset($_SESSION['user'])) {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $date_arr = explode('.', $lot['lot-date']);
            if(count($date_arr) !== 3) {
                $errors['lot-date'] = 'Дата введена неверно';
            } else if(strlen($date_arr[0]) !== 2 || strlen($date_arr[1]) !== 2 || strlen($date_arr[2]) !== 4) {
                $errors['lot-date'] = 'Дата введена неверно';
            } else if(!is_numeric($date_arr[0]) || !is_numeric($date_arr[1]) || !is_numeric($date_arr[2])) {
                $errors['lot-date'] = 'Дата введена неверно';
            } else if(!checkdate($date_arr[1], $date_arr[0], $date_arr[2])) {
                $errors['lot-date'] = 'Дата введена неверно';
            } else if(strtotime($lot['lot-date']) <= (time())) {
                $errors['lot-date'] = 'Дата введена неверно';
            }

            // Проверяем был ли загружен файл
            if($_FILES['image']['name']) {
                $tmp_name = $_FILES['image']['tmp_name'];
                $path = $_FILES['image']['name'];
                $file_type = mime_content_type($tmp_name);

                // Является ли тип файла разрешенным изображением?
                if(!array_search($file_type, PERMIT_MIME_TYPES)) {
                    $errors['image'] = 'Загрузите картинку лота в формате PNG или JPEG';
                }
            } else {
                $errors['image'] = 'Вы не загрузили изображение лота';
            }

            if(!$errors) {
                // Переместим из временной директории изображение и переименуем его
                $tmp_name = $_FILES['image']['tmp_name'];
                $path = $_FILES['image']['name'];
                $lot['image'] = remove_image($path, $tmp_name);
                $category_id = get_category_id($con, $lot['category']);

                $stmt = mysqli_prepare($con, ADD_LOT);
                mysqli_stmt_bind_param($stmt, 'ssssssi',
                                       $lot['lot-name'],
                                       $lot['message'],
                                       $lot['image'],
                                       $lot['lot-rate'],
                                       $lot['lot-date'],
                                       $lot['lot-step'],
                                       $category_id
                                      );
                $is_add = mysqli_stmt_execute($stmt);
                if($is_add) {
                    $lot_id = mysqli_insert_id($con);

                    header('Location: lot.php?id=' . $lot_id);
                } else {
                    http_response_code(500);
                    $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
                    $error_message = 'Попробуйте добавить лот позже.';

                    $categories_list = include_template('categories.php', ['categories'  => get_categories_list($con)]);
                    $fail_content = include_template('404.php', [
                                                                  'categories_list'      => $categories_list,
                                                                  'title'                => $error_title,
                                                                  'message'              => $error_message
                    ]);
                    $page = include_template('layout.php', [
                                                    'content'     => $fail_content,
                                                    'categories'  => get_categories_list($con),
                                                    'user_name'   => $user_name,
                                                    'is_auth'     => $is_auth,
                                                    'page_name'   => $page_name
                    ]);
                }
            }
        }


        $categories_list = include_template('categories.php', ['categories' => get_categories_list($con)]);
        $add_lot = include_template('add-lot.php', [
                                                       'categories_list' => $categories_list,
                                                       'categories'      => get_categories_list($con),
                                                       'errors'          => $errors
                                                   ]);
        $page = include_template('layout.php', [
                                                    'content'        => $add_lot,
                                                    'categories'     => get_categories_list($con),
                                                    'user_name'      => $user_name,
                                                    'is_auth'        => $is_auth,
                                                    'page_name'      => $page_name
        ]);

        print($page);
    } else {
        http_response_code(403);
        $error_title = 'Ошибка 403: Доступ закрыт';
        $error_message = 'Эта страница доступна только для зарегистрированных пользователей.';

        $categories_list = include_template('categories.php', ['categories' =>     get_categories_list($con)]);
        $fail_content = include_template('404.php', [
                                                        'categories_list'   =>     $categories_list,
                                                        'title'             =>     $error_title,
                                                        'message'           =>     $error_message
        ]);
        $page = include_template('layout.php', [
                                                    'content'               => $fail_content,
                                                    'categories'            => get_categories_list($con),
                                                    'user_name'             => $user_name,
                                                    'is_auth'               => $is_auth,
                                                    'page_name'             => $page_name
        ]);

        print($page);
    }
}
