<?php
namespace crm;

use MongoClient;
use MongoException;
use MongoId;
use MongoCursor;
use crm\ErrorMessage;

////////////////////////////////////////////////////////////////////////////////
// Базовый функционал системы
// -----------------------------------------------
// Класс-обёртка для работы с MongoDB
// 
// @author Друшка Александр <fenix-site@yandex.ru>
////////////////////////////////////////////////////////////////////////////////
class MongoConnect
{

    protected static $_instance;
    public $connection, $database, $collection;
    public $cursorID;

    /**
     * Основной метод - реализует вызов конструктора
     * @return object
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self(DB, USERNAME, PASS, DBHOST);
        }
        return self::$_instance;
    }

    /**
     * Конструктор.
     * Создаёт подключение к базе, а база заранее указана в
     * конфиге.
     * @throws  MongoConnectionException выводится, когда не удается подключиться к БД.
     * @throws  Exception при selectDB().
     */
    private function __construct($database, $username, $password, $host)
    {
        //"mongodb://{$username}:{$password}@{$host}"
        if (class_exists('MongoClient', false)) {
            try {
                //Костыль для аутентификации на dev.
                // Пока не разобрался как без него подключиться
                if ($username == '' && $password == '') {
                    $this->connection = new  MongoClient("mongodb://$host");
                } else {
                    $this->connection = new MongoClient("mongodb://$username:$password@$host");
                }
            } catch (Exception $e) {
                throw new MongoException(ErrorMessage::text(1));
            }
            if ($this->databaseExists($database)) {
                $this->database = $this->connection->selectDB($database);
            }
        }
    }


    //запрещаем клонирование объекта модификатором private
    private function __clone()
    {
    }

    //запрещаем клонирование объекта модификатором private
    private function __wakeup()
    {
    }


    /**
     * Проверяет, существует ли база данных.
     *
     * @param      string $dbName Имя базы данных
     * @return     boolean
     */
    public function databaseExists($dbName)
    {
        $dbsList = $this->connection->listDBs();
        $dbsList = $dbsList['databases'];

        foreach ($dbsList as $db) {
            if ($db['name'] === $dbName) {
                return true;
            }
        }
        return false;
    }

////////////////////////////////////////////////////////////////////////////////
    function __destruct()
    {
        // При убивании экземпляра класса необходимо закрыть соеденение с БД
        if ($this->connection) {
            $this->connection->close();
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function selectCollection($name)
    {
        $this->collection = $this->database->selectCollection($name);
        return $this->collection;
    }

////////////////////////////////////////////////////////////////////////////////
    public function command($c)
    {
        if ($this->database) {
            return $this->database->command($c);
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function execute($c)
    {
        if ($this->database) {
            return $this->database->execute($c);
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function insert($item)
    {
        if ($this->collection) {
            $this->collection->insert($item);
            return $item['_id'];
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function remove($criteria)
    {
        if ($this->collection) {
            $r = $this->collection->remove($criteria, array('safe' => true));
            return $r;
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function removeById($id)
    {
        if ($this->collection) {
            $criteria = array(
                '_id' => new MongoId($id),
            );
            $r = $this->collection->remove($criteria, array('safe' => true));
            return $r;
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function saveById($id, $item)
    {
        if ($this->collection) {
            $criteria = array(
                '_id' => new MongoId($id),
            );
            return $this->collection->update($criteria, $item);
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////
    public function findAll($criteria, $fields = [], $limit = false, $skip = 0)
    {
        if ($this->collection) {
            if (!empty($fields)) {
                $cursor = $this->collection->find($criteria, $fields);
            } else {
                $cursor = $this->collection->find($criteria);
            };
            if ($limit && ($skip > 0)) {
                return $cursor->skip($skip)->limit($limit);
            } else {
                if ($limit && ($skip == 0)) {
                    return $cursor->limit($limit);
                } else {
                    if (!$limit && ($skip > 0)) {
                        return $cursor->skip($skip);
                    } else {
                        return $cursor;
                    }
                }
            }
            return $cursor;
        } else {
            return false;
        }
    }

////////////////////////////////////////////////////////////////////////////////

    public function findOne($criteria, $fields = [], $limit = true, $skip = 0)
    {
        if ($this->collection) {
            if (!empty($fields)) {
                $cursor = $this->collection->find($criteria, $fields);
            } else {
                $cursor = $this->collection->find($criteria);
            };
            if ($limit && ($skip > 0)) {
                return $cursor->skip($skip)->limit($limit);
            } else {
                if ($limit && ($skip == 0)) {
                    return $cursor->limit($limit);
                } else {
                    if (!$limit && ($skip > 0)) {
                        return $cursor->skip($skip);
                    } else {
                        return $cursor;
                    }
                }
            }
            return $cursor;
        } else {
            return false;
        }
    }

    public function findAllArr($criteria, $fields = [], $limit = false, $skip = 0){
        $cursor = $this->findAll($criteria, $fields, $limit, $skip);
        return $this->fetchArr($cursor);
    }

    /**
     * Выводит результат findOne() в виде массива и сохраняет _id в курсоре
     *
     * @param $criteria
     * @param array $fields
     * @param bool $limit
     * @param int $skip
     * @return array
     */
    public function findOneArr($criteria, $fields = [], $limit = false, $skip = 0){
        $cursor = $this->findOne($criteria, $fields, $limit, $skip);
        $this->cursorID = $this->getCursor__Id($cursor);
        return $this->fetchArr($cursor);
    }

    /**
     * Вывод результата в виде массива
     * У нас есть стандартный метод iterator_to_array($cursor)
     * Но он возвращает данные в виде [_id => item, _id => item]
     * это не совсем удобно
     * Поэтому возвращаю в таком виде [0 => item, 1=> item],
     * а если элемент один, то просто [item]
     *
     * @param $cursor
     * @return array
     */
    public function fetchArr(MongoCursor $cursor){
        $arr = [];
        if(isset($cursor)){
            foreach ($cursor as $item){
                $arr[] = $item;
            }
        }
        if(count($arr) == 1){
            return $arr[0];
        }
        return $arr;
    }

    public function getCursor__Id(MongoCursor $cursor){
        $cursor->rewind();
        if($cursor->count() == 1){
            return $cursor->key();
        }
        return iterator_to_array($cursor);
    }

    /**
     * Закрывает подключение к БД,
     * освобождает переменные
     */
    public function close(){
        self::$_instance->connection->close();
        self::$_instance = null;
    }

}

?>