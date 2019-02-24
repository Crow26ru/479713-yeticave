-- Наполнение списка категорий
INSERT INTO categories (name) VALUES ('Доски и лыжи'),
                                     ('Крепления'),
                                     ('Ботинки'),
                                     ('Одежда'),
                                     ('Инструменты'),
                                     ('Разное');

-- Добавление пользователей
INSERT INTO users (
    email,
    password,
    name,
    contact
)
VALUES (
    'user.test@list.ru',
    'LamiJ1on!03',
    'Flint',
    'Some City, ul. Zelenaya, d. 2'
),
(
    'user.test2@list.ru',
    'YnIm8olOnE!00',
    'Lorry',
    'Some City, ul. Kashtanovaya, d. 37'
);

-- Добавление существующих лотов
INSERT INTO lots (
    name,
    description,
    image,
    start_rate,
    date_end,
    step_value,
    category_id,
    author_id
) VALUES (
    '2014 Rossignol District Snowboard',
    'Совершенно новая доска. Ни разу не использовалась.',
    'img/lot-1.jpg',
    10999,
    '2019-03-07 00:00:00',
    100,
    1,
    1
),
(
    'DC Ply Mens 2016/2017 Snowboard',
    'Откатал на ней один сезон.',
    'img/lot-2.jpg',
    159999,
    '2019-03-08 00:00:00',
    200,
    1,
    2
),
(
    'Крепления Union Contact Pro 2015 года размер L/XL',
    'Отличные крепления для ваших ботинок.',
    'img/lot-3.jpg',
    8000,
    '2019-03-07 00:00:00',
    100,
    2,
    2
),
(
    'Ботинки для сноуборда DC Mutiny Charocal',
    'Эти ботинки то что надо для катания на сноуорде.',
    'img/lot-4.jpg',
    10999,
    '2019-03-09 00:00:00',
    150,
    3,
    1
),
(
    'Куртка для сноуборда DC Mutiny Charocal',
    'Отличная теплая куртка для активного отдыха.',
    'img/lot-5.jpg',
    7500,
    '2019-03-10 00:00:00',
    60,
    4,
    2
),
(
    'Маска Oakley Canopy',
    'Маска с поляризационным фильтром. В ней не будет солнце слепить в глаза',
    'img/lot-6.jpg',
    5400,
    '2019-03-07 00:00:00',
    50,
    6,
    2
);

-- Добавление ставок
INSERT INTO rates (
    rate,
    user_id,
    lot_id
) VALUES (
    7900,
    1,
    5
),
(
    5500,
    2,
    6
);
