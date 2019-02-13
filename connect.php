<?php
// Константы для установки соединения
define("server", "localhost");
define("user", "root");
define("db", "yeticave");

// Константы SQL запросов
define(
    "categories_list",
    "SELECT name AS categories FROM categories;"
);
define(
    "new_lots_list",
    "SELECT
        lots.name,
        categories.name AS category,
        lots.start_rate AS price,
        lots.image
     FROM lots
     JOIN categories ON lots.category_id = categories.id
     ORDER BY date_add DESC;"
);

$con = mysqli_connect(server, user, "", db);
mysqli_set_charset($con, "utf8");

if (!$con) {
    print("Ошибка соединения: " . mysqli_connect_error());
} else {
    $res_categories = mysqli_query($con, categories_list);
    $rows_categories = mysqli_fetch_all($res_categories, MYSQLI_ASSOC);

    $res_lots = mysqli_query($con, new_lots_list);
    $rows_lots = mysqli_fetch_all($res_lots, MYSQLI_ASSOC);
}
