<?php


/**
 *      Автор Предко В.Н.
 * 
 *      Дата создания 13.10.21 11:00
 * 
 *      Дата изменения 14.10.21 14:29
 *  
 */

include_once $_SERVER['DOCUMENT_ROOT']."/Person.php";

if (class_exists("Person") == false) {

    throw new Exception("Ошибка:\nКласс Person не определён.", 1);
}

/**
 * class People
 * 
 * Загружает и хранит список идентификаторов записей в базе данных объектов Person.
 * При создании, загружает из базы данных список id в соответствии с условием $condition
 * и параметрами этого условия передаваемыми в массиве $values.
 * Условие должно соответствовать синтаксису SQL.
 * Параметры в условии заменяются знаком вопроса(?).
 * Значения параметров передаются в виде массива.
 * 
 * С помощью соответствующих функций возвращает массив объектов Person по хранимым id.
 * 
 * С помощью метода DeletePeople, удаляет из базы данных записи с указанными в массиве id
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
        
        foreach ($data as $key => $value) {
            
            $this->peopleIds[$value['id']] = $value['id'];
        }
    }


    public function GetPeople():array
    {
        $people = [];

        foreach ($this->peopleIds as $id) {
            
            $people[] = new Person($this->database, $id);
        }

        return $people;
    }

    public function DeletePeople(array $ids)
    {
        foreach ($ids as $id) {
            
            if (empty($this->peopleIds[$id])) {

                continue;
            }
            
            $person = new Person($this->database, $id);
            
            $person->DeleteFromDatabase();

            unset($this->peopleIds[$id]);
        }
    }

}

