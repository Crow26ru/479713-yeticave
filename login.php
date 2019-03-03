<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

$is_auth = 0;
$user_name = '';
$page_name = 'Вход - YetiCave';
$errors = false;

if(!$con) {
    http_response_code(500);
    $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
    $error_message = 'Сайт временно недоступен. Попробуйте зайти позже';
    $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
    print($page);
} else {
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
            $is_email = select_stmt_query($con, EMAIL_CHECK, [$login['email']]);

            if(empty($is_email)) {
                $errors['email'] = 'Проверьте введенный e-mail';
            } else {
                $result = select_stmt_query($con, USER_AUTH, [$login['email']]);
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

    $categories_content = include_template('categories.php', ['categories' => get_categories_db($con)]);
    $login_content = include_template('login.php',  [
        'categories_list' => $categories_content,
        'errors'          => $errors
    ]);
    $page = include_template('layout.php', [
        'content'         => $login_content,
        'categories'      => get_categories_db($con),
        'user_name'       => $user_name,
        'is_auth'         => $is_auth,
        'page_name'       => $page_name
    ]);

    print($page);
}
