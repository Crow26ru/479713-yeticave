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


if(isset($_GET['id'])) {
    $category_id = $_GET['id'];
}

// Если был передан номер страницы, то читаем его
$num_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if(!$con) {
    http_response_code(500);
    $error_title = 'Ошибка 500: Внутреняя ошибка сервера';
    $error_message = 'Сайт временно недоступен. Попробуйте зайти позже';
    $page = get_page_error($con, $error_title, $error_message, $user_name, $is_auth);
    print($page);
} else {
    $total_lots = select_stmt_query($con, TOTAL_LOTS_CATEGORY, [$category_id]);
    
    if(empty($total_lots)) {
        http_response_code(404);
        $title = 'Ошибка 404: Лоты не найдены';
        $message = 'Нет активных лотов в данной категории.';
        $page = get_page_error($con, $title, $message, $user_name, $is_auth);
        print($page);
    } else {
        $total_lots = intval($total_lots[0]['total']);
        $pages = ceil($total_lots / LOTS_PAGE);
        $ofset = ($num_page - 1) * LOTS_PAGE;
        
        $paginator = get_array_paginator($num_page, $pages);
        var_dump($paginator);
    }
}