<?php
$is_auth = rand(0, 1);

$user_name = "Семён"; // укажите здесь ваше имя
$page_name = "Главная - YetiCave";

// Массив категорий:
$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$categories_length = count($categories);

// Информация об объявлениях:
// name - Название
// category - Категория (значение берется из массива категорий)
// price - Цена
// image - URL картинки
$products = [
    0 => [
        "name" => "2014 Rossignol District Snowboard",
        "category" => $categories[0],
        "price" => 10999,
        "image" => "img/lot-1.jpg"
    ],
    1 => [
        "name" => "DC Ply Mens 2016/2017 Snowboard",
        "category" => $categories[0],
        "price" => 159999,
        "image" => "img/lot-2.jpg"
    ],
    2 => [
        "name" => "Крепления Union Contact Pro 2015 года размер L/XL",
        "category" => $categories[1],
        "price" => 8000,
        "image" => "img/lot-3.jpg"
    ],
    3 => [
        "name" => "Ботинки для сноуборда DC Mutiny Charocal",
        "category" => $categories[2],
        "price" => 10999,
        "image" => "img/lot-4.jpg"
    ],
    4 => [
        "name" => "Куртка для сноуборда DC Mutiny Charocal",
        "category" => $categories[3],
        "price" => 7500,
        "image" => "img/lot-5.jpg"
    ],
    5 => [
        "name" => "Маска Oakley Canopy",
        "category" => $categories[5],
        "price" => 5400,
        "image" => "img/lot-6.jpg"
    ],
];

// Функция для форматирования суммы
function show_price($price) {
    $price = ceil($price);
    $price = number_format($price, 0, "", " ");
    return $price . " &#8381;";
}

require("functions.php");

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
