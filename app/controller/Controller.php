<?php
namespace crm;
use crm\Flash;

abstract class Controller
{
    protected $registry;
    public $model;
    public $model_name;

    public function __construct($registry)
    {
        $this->registry = $registry;
        //Переменные по умолчанию для сообщений и ошибок
        $this->registry['template']->set('flash', Flash::getMessage('flash'));
        $this->registry['template']->set('error', Flash::getMessage('error'));
        $this->setModel($this->model_name);
    }

    /**
     * Установить модель
     * Так как тут игры с неймспейсами, приходится его дописывать
     * @param $model_name
     */
    public function setModel($model_name)
    {
        if (isset($model_name) && $model_name != '') {
            $model_name = __NAMESPACE__ . \DIRECTORY_SEPARATOR . $model_name;
            $mod = new $model_name($this->registry);
            $this->model = $mod;
        }
    }

    /**
     * Получение сохранённой модели. На самом деле метод почти бессмысленный,
     * так как возможно обращение $this->model
     * но возможно в дальнейшем здесь будет заложена какая - то логика,
     * вроде возврата модели по умолчанию
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    abstract function index();
}