<?php
/**
 * Класс для создания событий в системе.
 * Так как события должны отрабатывать после перезагрузки страницы,
 * ничего не остаётся кроме как засовывать их в сессию либо в базу.
 * Однако дёргать каждый раз базу для того, чтобы вывести всплывающее
 * сообщение - не совсем хорошо.
 * Поэтому для простых событий имеет смысл предусмотреть простой вывод
 * через сессию. А для тех, что касаются сохранения данных и тд - через базу.
 *
 * Итого у нас 2 типа событий.
 * Данный класс пока реализует вывод простых событий
 *
 * User: ADrushka
 * Date: 15.06.2017
 * Time: 17:59
 */

namespace crm;


class Event
{
    private $events = [];
    protected static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
    }

    //запрещаем клонирование объекта модификатором private
    private function __clone()
    {
    }

    //запрещаем клонирование объекта модификатором private
    private function __wakeup()
    {
    }

    public function setEvent($key, $val)
    {
        session_start();
        $this->events[$key] = $val;
        $_SESSION[$key] = $val;
    }

    public function getEvent($key)
    {
        if($this->events[$key] && !empty($this->events[$key])){
            return $this->events[$key];
        }
        session_start();
        if(isset($_SESSION[$key]) && !empty($_SESSION[$key])){
            $this->events[$key] = $_SESSION[$key];
            return $this->events[$key];
        }
        return false;
    }

    public function removeEvent($key)
    {
        unset($this->events[$key]);
        session_start();
        unset($_SESSION[$key]);
    }

    public function run($key)
    {
        //Запуск обработчика событий
        //Что то вроде Action::runEvent($self->getEvent[$key])
    }

}