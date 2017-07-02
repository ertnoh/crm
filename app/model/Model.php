<?
namespace crm;

use crm\Connect;
use crm\Validator;

/**
 * Абстрактный класс модель.
 * Класс наследуется от класса соединения с БД
 * в котором описаны все способы обращения к ней
 *
 * При создании модели открывается подключение к бд и
 * выбирается коллекция по умолчанию, которая находится в $this->table
 *
 * Class Model
 * @package crm
 * @author Друшка Александр <fenix-site@yandex.ru>
 */

abstract class Model extends Connect
{
//    public $registry;
//Хранит подключение к бд с выбранной коллекцией
//    public $conn;
//    //Таблица в бд - она же коллекция
//    public $table;


    /**
     * Вызов любого метода в конкретной модели с параметрами
     * для унификации в контроллере
     *
     * @param $name
     * @param array $param
     * @return mixed
     */
    public function getData($name, $param = array())
    {
        if (count($param)) {
            return $this->$name($param);
        } else {
            return $this->$name();
        }
    }




    abstract function validate($data);

    abstract function index();
}
