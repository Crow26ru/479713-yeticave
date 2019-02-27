<?php
// Модуль для соединения с БД
// Константы для установки соединения
define('server', 'localhost');
define('user', 'root');
define('db', 'yeticave');

$con = mysqli_connect(server, user, '', db);
mysqli_set_charset($con, 'utf8');
