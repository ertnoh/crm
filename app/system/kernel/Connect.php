<?php
namespace crm;

use crm\MongoConnect;
use crm\Permission;

/**
 * Класс обёртка для подключения к базе данных.
 * Содержит все методы для работы с базой
 *
 * User: ADrushka
 * Date: 17.06.2017
 * Time: 23:52
 */
class Connect extends MongoConnect
{
    public $registry;
    //Хранит подключение к бд с выбранной коллекцией
    public $conn;
    //Таблица в бд - она же коллекция
    public $table;

    /**
     * При создании класса открывается подключение к бд и
     * выбирается коллекция по умолчанию, которая находится в $this->table
     *
     * Model constructor.
     * @param $registry
     */
    public function __construct($registry = null)
    {
        $this->conn = MongoConnect::getInstance();
        //Это для того, чтобы можно было унаследовать Модель
        $this->selectTable($this->table);
        $this->setRegistry($registry);
    }

    public function setRegistry($registry)
    {
        if (isset($registry)) {
            $this->registry = $registry;
        }
    }

    /**
     * Метод для смены таблицы
     *
     * @param $table
     * @return bool
     */
    public function selectTable($table)
    {
        if (isset($table)) {
            return $this->conn->selectCollection($table);
        }
        return false;
    }

    /**
     * Возврат таблицы по умолчанию
     * Метод работает только если есть $this->table
     *
     * @return mixed
     */
    public function table()
    {
        if (isset($this->table)) {
            return $this->conn->selectCollection($this->table);
        }
        return false;
    }


    /**
     * Общий метод для обновления записи с параметрами
     *
     * @param array $old
     * @param array $new
     * @param array $option
     * @return mixed
     */
    public function update($old, $new, $option = [])
    {
        if (empty($option)) {
            $option = ["upsert" => false, 'multi' => false];
        }

        if (isset($old)) {
            $cursor = $this->conn->findAll($old);
            $items = $this->conn->fetchArr($cursor);

            if (!empty($items) || (isset($option['upsert']) && $option['upsert'])) {
                //Если не заданы определённые поля для обновления записи
                if (!isset($new['$set'])) {
                    //Поддержка id при вставке новой записи
                    if (!empty($items) && isset($items['id'])) {
                        $new['id'] = $items['id'];
                    } else {
                        $new['id'] = $this->generateId();
                    }
                }

                return $this->conn->collection->update($old, $new, $option);
            }
        }
        return false;
    }

    /**
     * Обновляет одну найденную запись.
     * Если запись не найдена, ничего не происходит
     *
     * @param array $old
     * @param array $new
     * @return bool
     */
    public function updateOne($old, $new)
    {
        return $this->update($old, $new);
    }

    /**
     * Обновляет запись, но если она не найдена, вставляет
     * новую
     * Метод аналогичен save, однако отличается параметрами вызова
     * Здесь $old и $new - могут быть 2 разными массивами.
     * Кроме того этот метод работает одинаково вне зависимости от
     * обновления страницы
     * Поиск записи происходит по массиву $old
     *
     * Если задан $set - обновляются только заданные поля
     *
     * @param array $old
     * @param array $new
     * @return bool
     */
    public function updateIns($old, $new)
    {
        $options = ["upsert" => true, 'multi' => false];
        return $this->update($old, $new, $options);
    }

    /**
     * Сохраняет новую запись в коллекцию по умолчанию
     * вне зависимости от того, была ли эта запись раньше
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateId();
        }
        return $this->conn->insert($data);
    }


    /**
     * Создание новой записи.
     * ВНИМАНИЕ!!!!! Для этого метода надо генерить id для первого сохранения!!
     *
     * Если в рамках одного подключения к бд (до обновления страницы)
     * происходит повторный запрос save, то данные в записи обновятся и
     * в результирующем массиве будет присутствовать ["nModified"]=> int(1)
     *
     * Однако при обновлении страницы будет каждый раз создаваться новая!!!!
     * запись
     *
     * Возвращает массив вида
     * array(4) {
     * ["ok"]=> float(1)
     * ["n"]=> int(0)
     * ["err"]=> NULL
     * ["errmsg"]=>
     * NULL
     * }
     *
     * В метод можно передать параметры, описанные ниже
     *
     * 'fsync' => true Если значение равно true,
     * то перед подтверждением удачного добавления
     * данных в бд, они в обязательном порядке записываются на жесткий диск
     * 'w' => 1, можно проводить операции записи-удаления-обновления
     * 'j' => false, Если значение равно true,
     * то перед подтверждением удачного добавления данных в бд,
     * они в обязательном порядке журналируются
     * wtimeout время в миллисекундах,
     * которое сервер будет ожидать подтверждения обновления
     * timeout время в миллисекундах,
     * которое клиент будет ожидать ответ от базы данных
     *
     * @param array|object $data
     * @param array $options
     * @return array
     */
    public function save($data, $options = [])
    {
        if (empty($options)) {
            $options = [
                'fsync' => true,
            ];
        }

        return $this->conn->collection->save($data, $options);
    }


    /**
     * Обновление одного поля
     *
     * @param $old
     * @param $new
     * @return bool
     */
    public function updateField($old, $new)
    {
        $newItem = ['$set' => $new];
        return $this->update($old, $newItem);
    }

    /**
     * Переименование названия поля
     *
     * @param array $old
     * @param array $new
     * @return bool
     */
    public function renameField($old, $new)
    {
        $newItem = ['$rename' => $new];
        return $this->update($old, $newItem);
    }


    /**
     * Удаляет запись в коллекции по умолчанию
     *
     * @param array $data
     * @return bool
     */
    public function delete($data)
    {
        if (isset($data)) {
            $cursor = $this->conn->findOne($data);
            $item = $this->conn->fetchArr($cursor);
            if (!empty($item)) {
                $this->conn->remove($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Взять обычную запись по id
     *
     * @param int $id
     * @return bool
     */
    public function getById($id)
    {
        if (isset($id)) {
            $cursor = $this->conn->findOne(['id' => $id]);
            return $this->conn->fetchArr($cursor);
        }
        return false;
    }

    /**
     * Удалить запись по id
     *
     * @param $id
     * @return bool
     */
    public function removeById($id)
    {
        if (isset($id)) {
            return $this->conn->collection->remove(['id' => $id], array('safe' => true));
        }
        return false;
    }

    /**
     * Обновление по id без создания записи
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateById($id, $data)
    {
        return $this->updateOne(['id' => $id], $data);
    }


    /**
     * Методы повторяющие класс MongoConnect
     *
     * Находит все записи по массиву поиска $criteria
     * Если указан массив полей, выводит только указанныеы поляы
     *
     * @param array $criteria
     * @param array $fields
     * @param bool $limit
     * @param int $skip
     * @return mixed
     */
    public function findAll($criteria, $fields = [], $limit = false, $skip = 0)
    {
        return $this->conn->findAll($criteria, $fields, $limit, $skip);
    }

    /**
     * Методы повторяющие класс MongoConnect
     * Находит одну запись и выводит аналогично findAllы
     *
     * @param array $criteria
     * @param array $fields
     * @param bool $limit
     * @param int $skip
     * @return mixed
     */
    public function findOne($criteria, $fields = [], $limit = true, $skip = 0)
    {
        return $this->conn->findOne($criteria, $fields, $limit, $skip);
    }

    public function findAllArr($criteria, $fields = [], $limit = false, $skip = 0)
    {
        return parent::findAllArr($criteria, $fields, $limit, $skip);
    }

    public function findOneArr($criteria, $fields = [], $limit = false, $skip = 0)
    {
        return parent::findOneArr($criteria, $fields, $limit, $skip);
    }

    /**
     * Методы повторяющие класс MongoConnect
     * Сохранение по __id
     *
     * @param string $id
     * @param $item
     * @return mixed
     */
    public function saveBy__Id($id, $item)
    {
        return $this->conn->saveById($id, $item);
    }

    /**
     * Методы повторяющие класс MongoConnect
     * Удаление записи по __id
     *
     * @param $id
     * @return mixed
     */
    public function removeBy__Id($id)
    {
        return $this->conn->removeById($id);
    }


    /**
     * Методы повторяющие класс MongoConnect
     * Вывод в виде массива [0 => [1 item], 1 => [2 item] ..]
     *
     * @param $cursor
     * @return mixed
     */
    public function fetchArr($cursor)
    {
        return $this->conn->fetchArr($cursor);
    }

    /**
     * Пока id будет формироваться таким вот образом
     *
     * @return mixed
     */
    public function generateId()
    {
        $newId = uniqid().bin2hex(openssl_random_pseudo_bytes(5));
//        $this->selectTable('Ids');
//        $table = $this->table;
//        $data = $this->findOneArr(["$table" => ['$exists' => true]]);
//        $newId = $data["$table"] + 1;
//        $this->saveBy__Id($this->cursorID, ["$table" => $newId]);
//        $this->table();
        return $newId;
    }

    /**
     * Закрывает подключение к БД,
     * освобождает переменные
     */
    public function close()
    {
        parent::close();
        $this->__destruct();
    }

}