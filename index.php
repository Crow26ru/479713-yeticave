<?php
$is_auth = rand(0, 1);

$user_name = "Семён"; // укажите здесь ваше имя
$page_name = "Главная - YetiCave";

// Функция для форматирования суммы
function show_price($price) {
    $price = ceil($price);
    $price = number_format($price, 0, "", " ");
    return $price . " &#8381;";
}

require("functions.php");
require("connect.php");

// $rows_categories в connect.php
// $rows_lots в connect.php
$categories = change_to_simple_array($rows_categories);
$categories_length = count($categories);
$products = $rows_lots;

$page_content = include_template("index.php",   [
                                                  "categories"    => $categories,
                                                  "products"      => $products
                                                ]);
$layout_content = include_template("layout.php", [
                                                   "content"    => $page_content,
                                                   "user_name"  => $user_name,
                                                   "is_auth"    => $is_auth,
                                                   "page_name"  => $page_name,
                                                   "categories" => $categories
                                                 ]);

print($layout_content);
