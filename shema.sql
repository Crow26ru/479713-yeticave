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
    password VARCHAR(64)  NOT NULL,
    name     VARCHAR(48)  NOT NULL,
    contact  VARCHAR(256) NOT NULL,
    avatar   VARCHAR(48)
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

-- Добавление связей между таблицами
ALTER TABLE lots
ADD FOREIGN KEY (category_id)
REFERENCES categories(id);

ALTER TABLE lots
ADD FOREIGN KEY (author_id)
REFERENCES users(id);

ALTER TABLE lots
ADD FOREIGN KEY (winner_id)
REFERENCES users(id);

ALTER TABLE rates
ADD FOREIGN KEY (user_id)
REFERENCES users(id);

ALTER TABLE rates
ADD FOREIGN KEY (lot_id)
REFERENCES lots(id);

-- Создание индекса для полнотекстового поиска
CREATE FULLTEXT INDEX lot_search ON lots(name, description);