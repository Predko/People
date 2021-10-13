<?php

echo '<pre>';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT']."/Person.php";
include_once $_SERVER['DOCUMENT_ROOT']."/People.php";
include_once $_SERVER['DOCUMENT_ROOT']."/DatabaseMysqlPDO.php";

$mysql = [
    'host' => 'localhost',
    'dbname' => 'People',
    'username' => 'people',
    'password' => '123456'
];

$sqlQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" .$mysql['dbname'] . "';";

$connection = new PDO("mysql:host=" . $mysql['host'], $mysql['username'], $mysql['password']);

$result = $connection->query($sqlQuery)->fetchAll();

// Если нет базы данных - создаём.
if ($result == false) {
    
    $query = "CREATE DATABASE " . $mysql['dbname'];

    $connection->exec($query);
}

$query = "SELECT TABLE_NAME FROM information_schema.tables " 
       . "WHERE TABLE_SCHEMA = '" . $mysql['dbname'] . "' " 
       . "AND TABLE_NAME  = 'people'";

$result = $connection->query($query)->fetchAll();

// Если нет таблицы - создаём.
if ($result == false) {
    
    $query = "USE " . $mysql['dbname'] . ";"
           . "CREATE TABLE people"
           . "(" 
           .    "id INTEGER AUTO_INCREMENT PRIMARY KEY UNIQUE KEY NOT NULL,"
           .    "firstname VARCHAR(20) NOT NULL,"
           .    "lastname VARCHAR(20) NOT NULL,"
           .    "birthday VARCHAR(10) NOT NULL,"
           .    "gender INTEGER DEFAULT 0,"
           .    "city VARCHAR(20) NOT NULL"
           . ")";
    
    $connection->exec($query);
}

$database = new DatabaseMysqlPDO($mysql['host'], $mysql['dbname'], $mysql['username'], $mysql['password']);

//$person1 = new Person($database, 0, 'Виктор', 'Предко', '07-05-1964', 'муж', 'Пацевичи');
$person2 = new Person($database, 0, 'Викторк', 'Предко', '07-05-1964', 'муж', 'Пацевичи');
//$person3 = new Person($database, 0, 'Викторе', 'Предко', '07-05-1964', 'муж', 'Пацевичи');
//$person4 = new Person($database, 0, 'Викторн', 'Предко', '07-05-1964', 'муж', 'Пацевичи');
//$person5 = new Person($database, 0, 'Викторг', 'Предко', '07-05-1964', 'муж', 'Пацевичи');

$person1 = new Person($database, 1);

$person3 = new Person($database, 3);

$person4 = new Person($database, 4);

$person5 = new Person($database, 5);

$people = new People($database, "", []);

var_export($people,1);

var_export($person1,1);
var_export($person2,1);
var_export($person3,1);
var_export($person4,1);
var_export($person5,1);

echo "</pre>";
