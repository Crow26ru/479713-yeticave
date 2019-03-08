<?php
// Запрос на получение списка категорий
define(
    'CATEGORIES_LIST',
    'SELECT id, name AS categories FROM categories;'
);

// Запрос на получение категории по её ID
define(
    'GET_CATEGORY',
    'SELECT name FROM categories WHERE id = ?;'
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

// Ставка пользователя
define(
    'USER_RATE',
    'SELECT rate AS max_rate, user_id
    FROM rates
    WHERE lot_id = ?
    ORDER BY rate DESC LIMIT 1;'
);

// Запрос даты окончания лота
define(
    'DATE_END',
    'SELECT date_end FROM lots WHERE id = ?;'
);

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

// Запрос на получение e-mail и имени пользователя по его ID
define(
    'FIND_USER_DATA',
    'SELECT email, name FROM users WHERE id = ?;'
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

// Запрос на получение ставок пользователя по лоту
define(
    'FIND_RATE',
    'SELECT * FROM rates WHERE lot_id = ? AND user_id = ?;'
);

// Запрос на получение количества найденых лотов
define(
    'FIND_LOTS_TOTAL',
    'SELECT count(*) AS total
    FROM lots
    WHERE MATCH(lots.name, lots.description) AGAINST(?) AND date_end > NOW();'
);

// Запрос для полнотекстового поиска лотов
define(
    'FIND_LOTS',
    'SELECT lots.id,
        lots.name,
        lots.description,
        lots.image,
        lots.start_rate AS cost,
        lots.date_end AS time,
        lots.step_value AS step,
        categories.name AS category,
        lots.author_id AS author
    FROM lots
    JOIN categories ON lots.category_id = categories.id
    WHERE MATCH(lots.name, lots.description) AGAINST(?) AND date_end > NOW()
    ORDER BY lots.date_add DESC
    LIMIT 9 OFFSET ?;'
);

// Запрос на получение количества лотов по категории
define(
    'TOTAL_LOTS_CATEGORY',
    'SELECT count(*) AS total FROM lots WHERE category_id = ? AND date_end > NOW();'
);

// Запрос на получение последних лотов (не более 9) со смещением
define(
    'LOTS_CATEGORY_LIST',
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
     LIMIT 9 OFFSET ?;'
);

// Сколько лотов отображать на странице
define('LOTS_PAGE', 9);

// Запрос на получение ставок пользователя
define(
    'USER_RATES',
    'SELECT
         rates.rate,
         lots.id,
         lots.image,
         lots.name,
         categories.name AS category,
         lots.date_end AS time,
         rates.date_add,
         lots.winner_id
     FROM rates
     JOIN lots ON lots.id = rates.lot_id
     JOIN categories ON categories.id = lots.category_id
     WHERE rates.user_id = ?
     ORDER BY rates.date_add DESC;'
);

// Запрос на получение всех лотов у которых дата завершения раньше текущей и нет победителя
define(
    'FINISHED_LOTS',
    'SELECT id, winner_id, name FROM lots WHERE date_end < NOW() AND winner_id IS NULL;'
);

// Обновление таблицы победителей
define(
    'UPDATE_LOT',
    'UPDATE lots
     SET winner_id = ?
     WHERE id = ?;'
);
