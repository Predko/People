
<?php

/**
 *      Автор Предко В.Н.
 * 
 *      Дата создания 13.10.21 16:00
 * 
 *      Дата изменения 14.10.21 14:00
 * 
 *      Страница для тестирования функциональности классов Person, People.
 *      При каждом обращении создаёт 5 записей со случайными параметрами.
 */

echo '<pre>';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT']."/Person.php";
include_once $_SERVER['DOCUMENT_ROOT']."/People.php";
include_once $_SERVER['DOCUMENT_ROOT']."/DatabaseMysqlPDO.php";

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$protocol   = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
$hostame    = $_SERVER['HTTP_HOST'];
$script     = $_SERVER['SCRIPT_NAME'];
$params     = $_SERVER['QUERY_STRING'];
$params     = (!empty($params)) ? "?$params" : "";

$currentUrl = $protocol . '://' . $hostame . $script . $params;

session_start();

$_SESSION['lastpage'] = $currentUrl;

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

const ALPHABET_RU = "АБВГДЕЖЗИКЛМНОПРСТУФХЦЧШЩЭЮЯабвгдежзиклмнопрстуфхцчшщэюя";
                     
const ALPHABET_EN = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

function GenerateRandomName($lang)
{
    $length = rand(3, 10);

    if ($lang) {
        
        $result = "" . mb_substr(ALPHABET_RU, rand(0,27), 1);

        for ($i=1; $i < $length; $i++) { 
            
            $j = rand(28,53);
            $result .= mb_substr(ALPHABET_RU, $j, 1);
        }

        return $result;
    }

    $result = "" . ALPHABET_EN[rand(0,25)];

    for ($i=1; $i < $length; $i++) { 
        
        $result .= ALPHABET_EN[rand(26,51)];
    }
    
    return $result;
}

for ($i=0; $i < 5; $i++) { 
    
    $lang = rand(0,1);
    new Person($database, 0, 
        GenerateRandomName($lang), 
        GenerateRandomName($lang), 
        date('d-m-Y', rand(time() - 50 * 365 * 24 * 3600, time())), 
        (rand(0,1)) ? 'муж' : 'жен',
        GenerateRandomName($lang)
    );
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People</title>
</head>
<body>

<table>
    <thead>
        <tr>
            <th>id</th>
            <th>Имя</th>
            <th>Фамилия</th>
            <th>Возраст</th>
            <th>Пол</th>
            <th>Город</th>
        </tr>
    </thead>
    <tbody>

<?php

// Пример выборки записей с id между 60 и 120:
// $people = new People($database, "id >= ? AND id <= ?", [60,120]);

// Загружаем все записи.
$people = new People($database, "", []);

$peopleList = $people->GetPeople();

foreach ($peopleList as $person) {
    
    $personObj = $person->GetPerson(); 

    //echo "\n" . json_encode($personObj,256);
    $tr = <<<END
<tr>
    <td>$personObj->id</td>
    <td>$personObj->firstname</td>
    <td>$personObj->lastname</td>
    <td>$personObj->age</td>
    <td>$personObj->gender</td>
    <td>$personObj->city</td>
    <td><a href='/delete.php?id=$personObj->id'>delete</a></td>
</tr>
END;
    echo $tr;
}

?>
    </tbody>
</table>
    
</body>
</html>

</pre>
