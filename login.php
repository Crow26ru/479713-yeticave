<?php
require_once('constants.php');
require_once('functions.php');
require_once('connect.php');

$categories = [];
$is_auth = 0;
$user_name = '';
$page_name = 'Вход - YetiCave';
$errors = false;


$rows_categories = mysqli_query($con, CATEGORIES_LIST);
$rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

// Преобразование двумерного ассоциативного массива в простой массив категорий
foreach($rows_categories as $value) {
    array_push($categories, $value['categories']);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST;

    // Массив сообщений об ошибках
    $errors = [];

    // Проверяем, что поля заполнены
    foreach($login as $key => $value) {
        if(empty($login[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    // Валидируем полученный e-mail
    // Если на предыдущем шаге e-mail пуст, то пропускаем этот шаг
    if(!isset($errors['email']) && !filter_var($login['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введен некорректный e-mail';
    } else {
        // Если не было ошибки валидации пароля, то:
        // Сверим есть ли e-mail в БД
        // Если есть, то сверим и пароль

        $stmt = mysqli_prepare($con, EMAIL_CHECK);
        mysqli_stmt_bind_param($stmt, 's', $login['email']);
        mysqli_stmt_execute($stmt);
        $is_email = mysqli_stmt_get_result($stmt);
        $is_email = mysqli_fetch_row($is_email);

        if(!$is_email) {
            $errors['email'] = 'Проверьте введенный e-mail';
        } else {
            $stmt = mysqli_prepare($con, USER_AUTH);
            mysqli_stmt_bind_param($stmt, 's', $login['email']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $pass = $result[0]['password'];
            $name = $result[0]['name'];

            if(!password_verify($login['password'], $pass)) {
                $errors['password'] = 'Вы ввели неверный пароль';
            } else {
                $_SESSION['user'] = $name;
                $_SESSION['email'] = $login['email'];
                header('Location: ./');
            }
        }
    }
}


$categories_content = include_template('categories.php', ['categories' => $categories]);
$login_content = include_template('login.php',  [
                                                  'categories_list' => $categories_content,
                                                  'errors'          => $errors
                                                ]);
$page = include_template('layout.php', [
                                                  'content'         => $login_content,
                                                  'categories'      => $categories,
                                                  'user_name'       => $user_name,
                                                  'is_auth'         => $is_auth,
                                                  'page_name'       => $page_name
]);

print($page);
