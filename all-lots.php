<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

if(isset($_GET['id']) && isset($_GET['page'])) {
    $category_id = $_GET['id'];
    $page = $_GET['page'];

    if(isset($_SESSION['user'])) {
        $user_name = $_SESSION['user'];
        $is_auth = 1;
    } else {
        $user_name = '';
        $is_auth = 0;
    }

    $categories = [];
    $rows_categories = mysqli_query($con, CATEGORIES_LIST);
    $rows_categories = mysqli_fetch_all($rows_categories, MYSQLI_ASSOC);

    foreach($rows_categories as $value) {
        array_push($categories, $value['categories']);
    }

    // Задаём смещение
    $ofset = ($page - 1) * 9;

    if($ofset === 0) {
        $stmt = mysqli_prepare($con, NEW_LOTS_CATEGORY_LIST);
        mysqli_stmt_bind_param($stmt, 's', $category_id);
        mysqli_stmt_execute($stmt);
        $lots = mysqli_stmt_get_result($stmt);
        $lots = mysqli_fetch_all($lots, MYSQLI_ASSOC);

        // Шаблоны без пагинациии
        $page_name = $lots[0]['category'] . ' - YetiCave';

        $categories_list = include_template('categories.php', ['categories'  => $categories]);
        $content = include_template('all-lots.php', [
                                                        'categories_list' => $categories_list,
                                                        'category'        => $lots[0]['category'],
                                                        'lots'            => $lots,
                                                        'paginator'       => ''
        ]);
        $page = include_template('layout.php', []);
    } else {
        $stmt = mysqli_prepare($con, NEW_LOTS_CATEGORY_LIST_OFSET);
        mysqli_stmt_bind_param($stmt, 'si', $category_id, $ofset);
        mysqli_stmt_execute($stmt);
        $lots = mysqli_stmt_get_result($stmt);
        $lots = mysqli_fetch_all($lots, MYSQLI_ASSOC);

        $page_name = $lots[0]['category'] . ' - YetiCave';
        // Шаблоны с пагинациией
    }
} else {
    http_response_code(404);
    $error_title = 'Ошибка 404: Страница не найдена';
    $error_message = 'Данной страницы не существует на сайте.';

    $categories_content = include_template('categories.php', ['categories'      => $categories]);

    $fail_content = include_template('404.php',              [
                                                              'categories_list' => $categories_content,
                                                              'title'           => $error_title,
                                                              'message'         => $error_message
    ]);

    $all_content = include_template('layout.php',            [
                                                              'content'         => $fail_content,
                                                              'categories'      => $categories,
                                                              'user_name'       => $user_name,
                                                              'is_auth'         => $is_auth,
                                                              'page_name'       => $page_name
                                                             ]);
    print($all_content);
}
