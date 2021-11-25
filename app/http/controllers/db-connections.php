<?php
use app\http\helpers\EnvController;

(new EnvController(__DIR__ . '/.env'))->load();

$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT');
$database = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

$connection_string = mysqli_connect($host, $user, $password, $database, $port);

if(!$connection_string) {
    die('Could not connect to database');
}