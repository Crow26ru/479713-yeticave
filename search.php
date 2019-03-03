<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

$page_name = 'Поиск лотов - YetiCave';

if(isset($_SESSION['user'])) {
    $user_name = $_SESSION['user'];
    $is_auth = 1;
} else {
    $user_name = '';
    $is_auth = 0;
}

if(!$con) {
    http_response_code(500);
    $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
    $error_message = 'Сайт временно недоступен. Попробуйте зайти позже';
    $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
    print($page);
} else {
    $search_words = $_GET['search'] ?? '';
    $search_words = trim($search_words);
        
    if($search_words) {
        // Выполнить запрос на получение лотов по поиску
        $stmt = mysqli_prepare($con, FIND_LOTS);
        mysqli_stmt_bind_param($stmt, 's', $search_words);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(empty($result)) {
            $title = 'Ошибка 404';
            $message = 'Ничего не найдено по вашему запросу';
            $page = get_page_error($con, $title, $message, $user_name, $is_auth);
            print($page);
        } else {
            // Подключение шаблонов
            $categories_content = include_template('categories.php', ['categories' => get_categories_db($con)]);

            // Временная заглушка, так как пока ещё не готова пагинация
            $paginator_placeholder = '';

            $page_content = include_template('search.php', [
                'categories_list' => $categories_content,
                'search_str'      => $search_words,
                'lots'            => $result,
                'paginator'       => $paginator_placeholder
            ]);
            $page = include_template('layout.php', [
                'content'         => $page_content,
                'categories'      => get_categories_db($con),
                'user_name'       => $user_name,
                'is_auth'         => $is_auth,
                'page_name'       => $page_name
            ]);

            print($page);    
        }
        
    // Если нам отправили пустую строку, то выведем страницу, что ничего не найдено
    } else {
        $title = 'Ошибка 404';
        $message = 'Ничего не найдено по вашему запросу';
        $page = get_page_error($con, $title, $message, $user_name, $is_auth);
        print($page);
    }
}