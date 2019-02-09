-- Создание БД
CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

-- Создание таблицы пользователей
CREATE TABLE users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email    VARCHAR(64)  NOT NULL UNIQUE,
    password VARCHAR(32)  NOT NULL,
    name     VARCHAR(48)  NOT NULL,
    contact  VARCHAR(256) NOT NULL,
    avatar   VARCHAR(48),
    lot_id   INT,
    rate_id  INT
);

-- Создание таблицы лотов
CREATE TABLE lots (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    date_add    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name        VARCHAR(64)  NOT NULL,
    description VARCHAR(256) NOT NULL,
    image       VARCHAR(48)  NOT NULL,
    start_rate  INT          NOT NULL,
    date_end    DATETIME     NOT NULL,
    step_value  INT          NOT NULL,
    category_id INT          NOT NULL,
    author_id   INT          NOT NULL,
    winner_id   INT
);

-- Создание таблицы категорий
CREATE TABLE categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL
);

-- Создание таблицы ставок
CREATE TABLE rates (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rate     INT NOT NULL,
    user_id  INT NOT NULL,
    lot_id   INT NOT NULL
);
