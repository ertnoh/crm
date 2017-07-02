<?php
namespace crm;

use crm\Model;

/**
 * Класс реализующий коллекцию users
 *
 * User: ADrushka
 * Date: 14.06.2017
 * Time: 1:53
 */
class Users extends Model
{
    public $table = 'users';

    public function index()
    {
//        $user = [
//            'login' => 'admin',
//            'pass' => 'pass',
//            'role' => 1,
//            'name' => 'Александр',
//            'fam' => 'Друшка',
//            'otchestvo' => 'Георгиевич',
//        ];
//
//        $a = $this->updateIns(['login'=>'admin'],$user);
//
//        var_dump($a);
    }

    /**
     * Удаляет хэш у пользователя, для того, чтобы его разлогинить
     */
    public function logout(){
        session_start();
        $hash = strip_tags(htmlentities($_SESSION['user'], ENT_QUOTES));
        //Определение пользователя и его прав1
        $auth = [
            '_sess_hash' => $hash,
        ];

        $user = $this->findOneArr($auth);
        if(isset($user) && isset($user['_sess_hash'])){
            $this->updateOne($auth, ['$set' => ['_sess_hash' => '']]);
        }
    }

    public function validate($data)
    {
        return Validator::str($data);
    }
}