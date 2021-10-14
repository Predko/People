<?php
/**
 *      Автор Предко В.Н.
 * 
 *      Дата создания 13.10.21 16:00
 * 
 *      Дата изменения 14.10.21 14:00
 * 
 *      Обработка запроса на удаление записи в базе данных по id.
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT']."/Person.php";
include_once $_SERVER['DOCUMENT_ROOT']."/People.php";
include_once $_SERVER['DOCUMENT_ROOT']."/DatabaseMysqlPDO.php";

session_start();

$mysql = [
    'host' => 'localhost',
    'dbname' => 'People',
    'username' => 'people',
    'password' => '123456'
];

$id = $_GET['id'];

$database = new DatabaseMysqlPDO($mysql['host'], $mysql['dbname'], $mysql['username'], $mysql['password']);

$people = new People($database, "id = ?", [$id]);

$people->DeletePeople([$id]);

header('Location: ' . $_SESSION['lastpage']);