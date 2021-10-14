<?php

/**
 *      Автор Предко В.Н.
 * 
 *      Дата создания 13.10.21 15:00
 * 
 *      Дата изменения 14.10.21 14:21
 * 
 * 
 */


include_once $_SERVER['DOCUMENT_ROOT']."/AbstractDatabase.php";

/**
 * Класс обеспечивает доступ к базе данных Mysql с помощью библиотеки PDO.
 */

class DatabaseMysqlPDO extends AbstractDatabase
{
    private $host;
    private $nameDatabase;
    private $connection;
    private $user;
    private $password;

    public function __construct($host, $name, $user, $password)
    {
      $this->host = $host;
      $this->nameDatabase = $name;
  
      $this->user = $user;
      $this->password = $password;
    }

    /**
     * Возвращает данные из базы данных по id.
     * Если такой id не найден, возвращает null.
     * @param int $id
     * @return array $data
     */
    public function Select(string $listFields, string $condition, array $values): ?array
    {
        $this->Open();
    
        $whereString = "";
        if (!empty($condition)) {

            $whereString = "WHERE $condition";
        }
        
        $query = "SELECT $listFields FROM $this->nameDatabase $whereString;";

        //echo "\nquery = $query" . "\nvalues = " . json_encode($values, 256);
        $stmt = $this->connection->prepare($query);

        $stmt->execute($values);
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //echo "\nresult = " . json_encode($result, 256);
        $this->Close();
    
        return ($result != false) ? $result : null;
    }

    /**
     * Создаёт запись в базе данных.
     * Принимает ассоциативный массив - вида: ['id' => value, 'Имя поля базы данных' => значение, ... ].
     * Возвращает id, если запись добавлена или 0, если нет.
     * @param array $data
     * @return int $id
     */
    public function Insert(array $data):int
    {
        $this->Open();

        $columns = array_keys($data);

        $columnsWithoutId = [];
        for ($i = 1; $i != count($columns); $i++)
        {
            $columnsWithoutId[] = $columns[$i];
        }

        $values = array_values($data);

        $valuesWithoutId = [];
        $template = [];
        for ($i = 1; $i != count($values); $i++)
        {
            $valuesWithoutId[] = $values[$i];
            
            $template[] = '?';
        }

        $query = "INSERT INTO $this->nameDatabase ("
                . implode(',', $columnsWithoutId)
                . ') VALUES ('
                . implode(',', $template)
                . ');';

        $stmt = $this->connection->prepare($query);

        $rowCount = $stmt->execute($valuesWithoutId);

        $id = $this->connection->lastInsertId();        
        
        $this->Close();
        
        if ($rowCount == 0)
        {
            return 0;
        }
    
        return $id;
    }

    /**
     * Обновляет данные в базе данных.
     * Принимает ассоциативный массив - вида: ['id' => value, 'Имя поля базы данных' => значение, ... ].
     * Возвращает 1, если обновление успешно и 0 если нет.
     * @param array $data
     * @return int
     */
    public function Update(array $data):int
    {
        $this->Open();

        $setData = [];

        $index = 0;

        $values = [];

        foreach ($data as $key => $value)
        {
            if ($index++ == 0) {
                continue;
            }

            $values[] = $value;

            $setData[] = ' ' . $key . ' = ?';
        }

        $values[] = $data['id'];

        $query = "UPDATE $this->nameDatabase SET "
                . implode(',', $setData)
                . ' WHERE id = ?;';

        $stmt = $this->connection->prepare($query);

        $rowCount = $stmt->execute($values);
        
        $this->Close();
        
        return $rowCount;
    }

    /**
     * Удаляет запись из базы данных по id.
     * @param int $id
     */
    public function DeleteData(int $id)
    {
        
        $this->Open();

        $query = "DELETE FROM $this->nameDatabase WHERE id = ?;";

        $stmt = $this->connection->prepare($query);

        $rowCount = $stmt->execute([$id]);
        
        $this->Close();
        
        return $rowCount;
    }

    // Открываем подключение.
    private function Open()
    {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$this->nameDatabase;charset=utf8mb4", 
                                    $this->user, $this->password);
    }

    // Закрываем подключение.
    private function Close()
    {
        $this->connection = null;
    }

}