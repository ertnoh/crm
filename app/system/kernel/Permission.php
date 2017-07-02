<?php
/**
 * Права
 *
 * User: ADrushka
 * Date: 16.06.2017
 * Time: 1:22
 */
namespace crm;

use crm\Connect;


class Permission
{
    protected static $_instance;
    private $perm_arr = [];
    private $perm_arrdb = [];
    public $role;

    public static function getPermission($registry)
    {
        if (self::$_instance === null) {
            self::$_instance = new self($registry);
        }
        return self::$_instance;
    }

    /**
     * TODO Доделать получение юзера
     *
     * Permission constructor.
     * @param $registry
     */
    private function __construct($registry)
    {
        session_start();
        if(!isset($_SESSION['user']) || $_SESSION['user'] == ''){
            $this->perm_arr = [];
            $this->role = 0;
            $this->perm_arrdb = [];
        }

        $hash = strip_tags(htmlentities($_SESSION['user'], ENT_QUOTES));
        //Определение пользователя и его прав1
        $auth = [
            '_sess_hash' => $hash,
        ];

        //Соединяемся с базой данных, коллекция users
        $conn = new Connect();
        $conn->selectTable('users');
        $user = $conn->findOneArr($auth);
//        var_dump($user, $hash);
//        die();

        //Заполняем права
        if (isset($user) && isset($user['role'])) {
            $this->role = $user['role'];

            $conn->selectTable('role');
            $perm = $conn->findOneArr(['role' => $user['role']]);

            $this->perm_arr = $perm['sys_perm'];
            $this->perm_arrdb = $perm['db_perm'];
        } else {
            $this->perm_arr = [];
            $this->role = 0;
            $this->perm_arrdb = [];
        }
        //Не уверен, надо ли здесь закрывать соединение с бд
//        $conn->close();
        unset($conn);
    }

    /**
     * Основной метод проверки прав
     * в perm_arr у нас содержатся список контроллеров, к которым
     * есть доступ у данного user. В дальнейшем можно в этот же
     * массив загонять права и на методы контроллера
     * all - означает права администратора
     *
     * @param string $controller
     * @return bool
     */
    public function checkPerm($controller, $action = 'index')
    {
        if (!isset($controller)) {
            return false;
        }

        //Особенный случай - выход из системы работает всегда
        if($controller == 'HomePage' && $action == 'logout'){
            return true;
        }

        if (empty($this->perm_arr)) {
            return false;
        }

        if (in_array("all", $this->perm_arr, true)) {
            return true;
        }


        if (isset($controller) && !in_array($controller, $this->perm_arr, true)) {
            return false;
        }

        return false;
    }
}