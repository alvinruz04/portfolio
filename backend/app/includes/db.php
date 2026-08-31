<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = (string) config('database.host', 'localhost');
$port = (int) config('database.port', 3306);
$name = (string) config('database.name', '');
$username = (string) config('database.username', '');
$password = (string) config('database.password', '');

if ($name === '' || $username === '') {
    throw new RuntimeException('Database configuration is incomplete.');
}

if ((string) config('env', 'production') === 'production' && $username === 'root') {
    throw new RuntimeException('Do not use the MySQL root account in production.');
}

$conn = mysqli_init();

if (!$conn) {
    throw new RuntimeException('Unable to initialize the database connection.');
}

$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$conn->real_connect($host, $username, $password, $name, $port);
$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+00:00'");
