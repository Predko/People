<?php

/**
* class Database
* 
* Абстрактный класс, выполняющий операции с базой данных.
*/
abstract class AbstractDatabase
{
    /**
     * Возвращает данные из базы данных по id.
     * Если такой id не найден, возвращает null.
     * @param int $id
     * @return array $data
     */
    abstract public function Select(string $listFields, string $condition, array $values): ?array;

    /**
     * Создаёт запись в базе данных.
     * Принимает ассоциативный массив - вида: ['id' => value, 'Имя поля базы данных' => значение, ... ].
     * Возвращает id, если запись добавлена или 0, если нет.
     * @param array $data
     * @return int $id
     */
    abstract public function Insert(array $data):int;

    /**
     * Обновляет данные в базе данных.
     * Принимает ассоциативный массив - вида: ['id' => value, 'Имя поля базы данных' => значение, ... ].
     * Возвращает 1, если обновление успешно и 0 если нет.
     * @param array $data
     * @return int
     */
    abstract public function Update(array $data):int;

    /**
     * Удаляет запись из базы данных по id.
     * @param int $id
     */
    abstract public function DeleteData(int $id);

    /**
     * Записывает данные в базу данных.
     * Принимает ассоциативный массив - вида: ['id' => value, 'Имя поля базы данных' => значение, ... ].
     * Если id == 0, создаёт новую запись,
     * иначе -  обновляет данные.
     * Возвращает id при успешном добавлении/обновлении или 0, при ошибке.
     * @param array $data
     * @return int $id
     */
    public function SetData(array $data): int
    {
        if ($data["id"] == 0) {
            return $this->Insert($data);
        }

        return $this->Update($data);
    }
}
