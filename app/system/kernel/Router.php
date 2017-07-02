<?php
namespace crm;

use crm\ErrorMessage;
use crm\SpecialPage;
use crm\Permission;

class Router
{
    private $registry;
    private $controller_path = CONTROLLER_FOLDER;
    private $args = [];

    function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Для установки пути к папке контроллеров
     *
     * @param string $path
     * @return bool
     */
    function setControllerPath($path)
    {
        $path = trim($path, '/\\');
        $path .= DIRECTORY_SEPARATOR;

        if (is_dir($path) == false) {
            return false;
        }
        $this->controller_path = $path;
        return true;
    }

    /**
     * Получение контроллера и действия
     * Разбор пути
     * Путь строится таким образом:
     * http://crm/[имя папки]/[имя папки]/[контроллер]/[метод в контроллере]
     *
     * @param $file
     * @param $controller
     * @param $action
     * @param $args
     */
    private function getController(&$file, &$controller, &$action, &$args)
    {
        $route = (empty($_GET['route'])) ? '' : $_GET['route'];

        $this->validate($route);

        // Получаем раздельные части
        $route = trim($route, '/\\');
        $parts = explode('/', $route);

        // Находим правильный контроллер
        $cmd_path = $this->controller_path;
        foreach ($parts as $part) {
            $fullpath = $cmd_path . $part;

            // Есть ли папка с таким путём?
            if (is_dir($fullpath)) {
                $cmd_path .= $part . DIRECTORY_SEPARATOR;
                array_shift($parts);
                continue;
            }

            // Находим файл контроллера
            // Если файла не существует,
            // то это либо Home контроллер либо ошибка пути
            if (is_file($fullpath . '.php')) {
                $controller = $part;
                array_shift($parts);
            }
            break;
        }

        if (empty($controller)) {
            $controller = 'HomePage';
        };

        // Получаем действие
        $action = array_shift($parts);

        if (empty($action)) {
            $action = 'index';
        }

        //Проверка прав
        $perm = Permission::getPermission($this->registry);
        if (!$perm->checkPerm($controller, $action)) {
            $this->redirect404(ErrorMessage::text(2));
        }

        $file = $cmd_path . $controller . '.php';
        $args = $parts;
    }


    function delegate()
    {
        // Анализируем путь
        $this->getController($file, $controller, $action, $args);

        // Файл доступен?
        if (is_readable($file) == false) {
            $this->redirect404();
        }

        // Создаём экземпляр контроллера
        $class = __NAMESPACE__ . \DIRECTORY_SEPARATOR . $controller;
        $controller = new $class($this->registry);

        // Действие доступно?
        if (is_callable(array($controller, $action)) == false) {
            $this->redirect404();
        }

        // Выполняем действие
        $controller->$action($args);
    }

    /**
     * Проверка существования адреса.
     * По большому счёту тут надо бы получить список контроллеров и
     * действий и затем проверить, существует ли такой путь в нашей системе
     *
     * TODO доделать хорошую валидацию
     * @param $route
     */
    private function validate($route)
    {
        if ($route != '') {
            $valid = preg_match("/^(\w+\/)*\w+\/*$/", $route);
            if (!$valid) {
                $this->redirect404();
            }
        }
    }

    //Редиректим на 404 страницу
    private function redirect404($message = '')
    {
        SpecialPage::redirect404($message);
    }
}