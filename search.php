<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');
require_once('update-db.php');

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
    $num_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $link = 'search.php?page=';
        
    if($search_words) {
        // Ищем сколько было вообще найдено лотов 
        $total_lots = select_stmt_query($con, FIND_LOTS_TOTAL, [$search_words]);
        
        if($total_lots[0]['total'] === 0) {
            $title = 'Ошибка 404';
            $message = 'Ничего не найдено по вашему запросу';
            $page = get_page_error($con, $title, $message, $user_name, $is_auth);
            print($page);
        } else {
            $total_lots = intval($total_lots[0]['total']);
            $pages = intval(ceil($total_lots / LOTS_PAGE));
            $ofset = ($num_page - 1) * LOTS_PAGE;
            
            // Формируем данные для пагинации
            $paginator = get_array_paginator($num_page, $pages);
            
            // Выполнить запрос на получение лотов по поиску
            $result = select_stmt_query($con, FIND_LOTS, [$search_words, $ofset]);
            
            // Подключение шаблонов
            $categories_content = include_template('categories.php', ['categories' => get_categories_db($con)]);
            
            $paginator_content = include_template('paginator.php', [
                'paginator'   => $paginator,
                'active_page' => $num_page,
                'link'        => $link,
                'total_pages' => $pages
            ]);

            $page_content = include_template('search.php', [
                'categories_list' => $categories_content,
                'search_str'      => $search_words,
                'lots'            => $result,
                'paginator'       => $paginator_content
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