<?php


/**
 * БД содержит поля:
 *  id, имя(только буквы), фамилия(только буквы), дата рождения,
 *  пол(0,1), город рождения
 *  Класс должен иметь поля:
 *  массив с id людей
 *  Класс должен иметь методы:
 *  1. Конструктор ведет поиск id людей по всем полям БД (поддержка
 *  выражений больше, меньше, не равно);
 *  2. Получение массива экземпляров класса 1 из массива с id людей
 *  полученного в конструкторе;
 *  3. Удаление людей из БД с помощью экземпляров класса 1 в
 *  соответствии с массивом, полученным в конструкторе.
 *  
 */

include_once $_SERVER['DOCUMENT_ROOT']."/Person.php";

if (class_exists("Person") == false) {

    throw new Exception("Ошибка:\nКласс Person не определён.", 1);
}

/**
* class People
* 
*/
class People
{
    private array $peopleIds;

    private AbstractDatabase $database;

    public function __construct(AbstractDatabase $database, string $condition, array $values)
    {
        $this->database = $database;
        
        // Извлекаем данные из базы данных.
        $data = $this->database->Select("id", $condition, $values);

        if ($data == null) {
            throw new Exception("Ошибка создания объекта: записи с такими условиями, не найдена.", 1);
        }
        
        echo "\n" . var_export($data, true);

        foreach ($data as $key => $value) {
            
            $this->peopleIds[] = $value['id'];
        }

        echo "\n" . var_export($this->peopleIds, true);
    }



}

