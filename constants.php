<?php
// Запрос на получение списка категорий
define(
    'CATEGORIES_LIST',
    'SELECT id, name AS categories FROM categories;'
);

// Запрос на получение последних лотов (не более 9)
define(
    'NEW_LOTS_LIST',
    'SELECT
        lots.id,
        lots.name,
        categories.name AS category,
        lots.start_rate AS price,
        lots.image,
        lots.date_end AS time
     FROM lots
     JOIN categories ON lots.category_id = categories.id
     WHERE date_end > NOW()
     ORDER BY date_add DESC
     LIMIT 9;'
);

// Список разрешенных MIME файлов
define('PERMIT_MIME_TYPES', ['image/pjpeg', 'image/jpeg', 'image/png']);

// Запрос на добавление лота
define(
    'ADD_LOT',
    'INSERT INTO lots (
        name,
        description,
        image,
        start_rate,
        date_end,
        step_value,
        category_id,
        author_id
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?);'
);

// Запрос e-mail из таблицы users
define(
    'EMAIL_CHECK',
    'SELECT email FROM users WHERE email = ?;'
);

// Запрос для аутентификации
define(
    'USER_AUTH',
    'SELECT email, password, name
     FROM users
     WHERE email = ?;'
);

//lot.php
// Запрос списка лотов
define(
    'LOT',
    'SELECT lots.name,
        lots.description,
        lots.image,
        lots.start_rate AS start_rate,
        lots.date_end AS time,
        lots.step_value AS step,
        categories.name AS category,
        lots.author_id AS author
    FROM lots
    JOIN categories ON lots.category_id = categories.id
    WHERE lots.id = ?;'
);

// Запрос истории ставок
define(
    'RATES_HISTORY',
    'SELECT
        users.name,
        rates.rate,
        rates.date_add AS time
    FROM rates
    JOIN users ON users.id = rates.user_id
    WHERE rates.lot_id = ?
    ORDER BY rates.date_add DESC;'
);

// Запрос последней ставки она же максимальная ставка
define(
    'LAST_RATE',
    'SELECT max(rates.rate) AS max_rate
    FROM rates
    WHERE rates.lot_id = ?;'
);

// Запрос даты окончания лота
define(
    'DATE_END',
    'SELECT date_end FROM lots WHERE id = ?;'
);

// Запрос на получение шага ставки
/*
define(
    'STEP_RATE',
    'SELECT step_value FROM lots WHERE id = ?;'
);
*/

// Запрос на добавление ставки
define(
    'ADD_RATE',
    'INSERT INTO rates (
        rate,
        user_id,
        lot_id
    )
    VALUES (?, ?, ?);'
);

// Запрос на получение ID пользователя
define(
    'FIND_USER',
    'SELECT id FROM users WHERE email = ?;'
);

// Запрос на добавление пользователя в табицу users
define(
    'ADD_USER',
    'INSERT INTO users (
         email,
         password,
         name,
         contact,
         avatar
    )
    VALUES (?, ?, ?, ?, ?);'
);

// Запрос на получение последних лотов (не более 9) без смещения
define(
    'NEW_LOTS_CATEGORY_LIST',
    'SELECT
        lots.id,
        lots.name,
        categories.name AS category,
        lots.start_rate AS price,
        lots.image,
        lots.date_end AS time
     FROM lots
     JOIN categories ON lots.category_id = categories.id
     WHERE date_end > NOW() AND category_id = ?
     ORDER BY date_add DESC
     LIMIT 9;'
);

// Запрос на получение последних лотов (не более 9) со смещением
define(
    'NEW_LOTS_CATEGORY_LIST_OFSET',
    'SELECT
        lots.id,
        lots.name,
        categories.name AS category,
        lots.start_rate AS price,
        lots.image,
        lots.date_end AS time
     FROM lots
     JOIN categories ON lots.category_id = categories.id
     WHERE date_end > NOW() AND category_id = ?
     ORDER BY date_add DESC
     LIMIT 9 OFSET ?;'
);

// Запрос на получение ставок пользователя по лоту
define(
    'FIND_RATE',
    'SELECT * FROM rates WHERE lot_id = ? AND user_id = ?;'
);

// Запрос для полнотекстового поиска лотов
define(
    'FIND_LOTS',
    'SELECT lots.name,
        lots.description,
        lots.image,
        lots.start_rate AS cost,
        lots.date_end AS time,
        lots.step_value AS step,
        categories.name AS category,
        lots.author_id AS author
    FROM lots
    JOIN categories ON lots.category_id = categories.id
    WHERE MATCH(lots.name, lots.description) AGAINST(?);'
);