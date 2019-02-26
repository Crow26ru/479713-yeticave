<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

$errors = false;

// is_auth потом надо будет как-то изменить, а user_name отображать после добавления пользователя
$is_auth = 0;
$user_name = '';

$page_name = 'Регистрация - YetiCave';
$categories = [];

//Получаем список категорий из БД
$rows_categories = mysqli_query($con, CATEGORIES_LIST);
$rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

//Разбор двумерного массива категорий из БД в одномерный масссив категорий
foreach($rows_categories as $category) {
    array_push($categories, $category['categories']);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Запишем массив данных, полученных при регистрации нового пользователя
    $user_data = $_POST;

    // Массив с сообщениями об ошибках валидации
    $errors = [];

    // Массив с именами обязательных полей
    $required_fields = ['email', 'password', 'name', 'message'];

    // Проверяем, что обязательные поля не пусты
    foreach($required_fields as $key) {
        if(empty($user_data[$key])) {
            $errors[$key] = 'Это поле является обязательным для заполнения.';
        }
    }

    // Валидация введенного e-mail
    // Если e-mail пройдет валидацию на формат, проверим будет ли хотя бы одно совпадение e-mail в таблице users
    if(!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Вы ввели некорректный e-mail.';
    } else {
        // Экранируем из введенного email спецсимволы
        $user_data['email'] = mysqli_real_escape_string($con, $user_data['email']);

        $stmt = mysqli_prepare($con, EMAIL_CHECK);
        mysqli_stmt_bind_param($stmt, 's', $user_data['email']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $result = mysqli_fetch_row($result);

        // Если результат запроса не NULL, то сообщим, что e-mail занят
        if($result) {
            $errors['email'] = 'Адрес этого e-mail занят.';
        }
    }

    // Проверяем добавил ли пользователь аватарку. Если аватарка добавлена, то пройдем процедуру валидации
    if($_FILES['image']['name']) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = $_FILES['image']['name'];
        $file_type = mime_content_type($tmp_name);

        // Является ли тип файла разрешенным изображением?
        if(!array_search($file_type, PERMIT_MIME_TYPES)) {
            $errors['image'] = 'Аватарка должна быть в формате PNG или JPEG';
        }
    }

    // Если ошибок нет, то выполним запрос на добавление нового пользователя,
    // а если есть аватарка, то переместим её из временной папки
    if(!$errors) {
        // Надо будет для начала захешировать пароль пользователя
        $pass = password_hash($user_data['password'], PASSWORD_DEFAULT);

        // Выполняем запрос на добавление пользователя в таблицу users
        $stmt = mysqli_prepare($con, ADD_USER);
        mysqli_stmt_bind_param($stmt, 'sssss', $user_data['email'],
                                               $pass,
                                               $user_data['name'],
                                               $user_data['message'],
                                               $user_data['image']
        );
        $is_add = mysqli_stmt_execute($stmt);

        //Если пользователь успешно добавлен, то перенаправим его на главную страницу
        if($is_add) {
            // Если была получена аватарка, то нужно её переместить и переименовать
            if($_FILES['image']['name']) {
                $tmp_name = $_FILES['image']['tmp_name'];
                $path = $_FILES['image']['name'];
                   $user_data['image'] = remove_image($path, $tmp_name);
            } else {
                $user_data['image'] = '';
            }

            // Сразу залогинем пользователя после регистрации
            session_start();
            $_SESSION['user'] = $user_data['name'];
            $_SESSION['email'] = $user_data['email'];
            header('Location: ./');
        }
    }
}

$categories_list = include_template('categories.php', ['categories' => $categories]);
$add_user = include_template('sign-up.php', [
                                               'categories_list' => $categories_list,
                                               'errors'          => $errors
                                           ]);
$page = include_template('layout.php', [
                                            'content'        => $add_user,
                                            'categories'     => $categories,
                                            'user_name'      => $user_name,
                                            'is_auth'        => $is_auth,
                                            'page_name'      => $page_name
]);

print($page);
