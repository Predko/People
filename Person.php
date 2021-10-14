<?php

/**
 *      Автор Предко В.Н.
 * 
 *      Дата создания 13.10.21 10:00
 * 
 *      Дата изменения 14.10.21 14:21
 * 
 * 
 */

include_once $_SERVER['DOCUMENT_ROOT']."/AbstractDatabase.php";

const MALE = 'муж';
const FEMALE = 'жен';

const NAME_PATTERN = "/[^a-zA-Zа-яёА-ЯЁ]/";

const DATE_FORMAT = 'd-m-Y';

const ID = 0;
const FIRSTNAME = 1;
const LASTNAME = 2;
const BIRTHDAY = 3;
const GENDER = 4;
const CITY = 5;

/**
 * class Person
 *
 *   
*/
class Person
{
    private $id;
    private $firstname;
    private $lastname;
    private $birthday;   // в виде строки формата: "d-m-Y"; напр.: ('01-01-2010')
    private $gender;     // 0 - муж, 1 - жен.
    private $city;

    private AbstractDatabase $database;

    /**
     * Инициализирует объект данными.
     * Проверяет корректность данных.
     * Если данные некорректны, выбрасывает исключение.
     * Если в базе данных уже есть запись с таким id - обновляет данные в базе данных.
     * Если такой записи нет - добавляет запись в базу данных.
     */
    public function __construct(AbstractDatabase $database, int $id, string $firstname = '', string $lastname = '', 
        string $birthday = '', string $gender = '0', string $city = ''
    ) {
        $this->database = $database;
        
        if ($id != 0) {
            // Извлекаем данные из базы данных.
            $data = $this->database->Select("*", "id = ?", [$id])[0];

            if ($data == null) {
                throw new Exception("Ошибка создания объекта: запись с таким id, не найдена.", 1);
            }

            $this->SetDataFromArray($data);
        }
        else {
            // Проверяем корректность данных и записываем их в базу данных.
            
            // Корректность имён.
            $firstname = $this->GetValidName($firstname);
            $lastname = $this->GetValidName($lastname);
            
            if ($firstname == null || $lastname == null) {
                throw new Exception("Имя или Фамилия некорректны:\nИмя: $firstname\nФамилия: $lastname\n", 1);
            }

            // Корректность даты.
            $d = DateTime::createFromFormat(DATE_FORMAT, $birthday);

            if (!$d || $d->format(DATE_FORMAT) != $birthday) {
                throw new Exception("Некорректная дата рождения: $birthday", 1);
            }
            
            $this->SetData($id, $firstname, $lastname, $birthday, $gender, $city);

            $id = $this->SaveToDatabase();

            if ($id == 0) {
                throw new Exception("Ошибка: не удалось добавить объект в базу данных.", 1);
            }
        }
    }

    public function SaveToDatabase()
    {
        return $this->database->SetData($this->GetArray());
    }

    public function DeleteFromDatabase()
    {
        $this->database->DeleteData($this->id);
    }

    /**
     * Возвращает возраст в виде количества полных лет.
     * @param string Дата в виде строки, формата: DATE_FORMAT.
     * @return int возраст, полных лет.
     */
    public static function Age($birthday): int
    {
        return date_diff(
            date_create_from_format(DATE_FORMAT, $birthday), 
            date_create('now'))->y;
    }

    public static function GenderToString(int $gender): string
    {
        return ($gender == 0) ? MALE : FEMALE;
    }

    /**
     * Возвращает имя с удалёнными пробелами вначале и конце.
     * Если имя не соответствует шаблону имени, возвращает null.
     */
    private function GetValidName(string $name):?string
    {
        $name = trim($name);

        if (mb_strlen($name) == 0 || preg_match("/[^a-zA-Zа-яёА-ЯЁ]+/mui", $name) == 1) {
            return null;
        }

        return $name;
    }

    public function SetDataFromArray(array $data)
    {
        $this->id = $data['id'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->birthday = $data['birthday'];
        $this->gender = $data['gender'];
        $this->city = $data['city'];
    }

    public function SetData(int $id, string $firstname, 
        string $lastname, string $birthday, string $gender, string $city
    ) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthday = $birthday;
        $this->gender = ($gender == 'муж' || $gender == '0') ? 0 : 1;
        $this->city = $city;
    }

    public function GetArray(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'city' => $this->city,
        ];
    }

    public function GetPerson()
    {
        $person = new stdClass();

        $person->id = $this->id;
        $person->firstname = $this->firstname;
        $person->lastname = $this->lastname;
        $person->age = $this->Age($this->birthday);
        $person->gender = $this->GenderToString($this->gender);
        $person->city = $this->city;

        return $person;
    }
}


