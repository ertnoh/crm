<?php
namespace crm;

use crm\SpecialPage;
use crm\Permission;
use crm\Registry;
use crm\Flash;

/**
 * Общая точка входа для всех ajax запросов
 *
 * User: ADrushka
 * Date: 16.06.2017
 * Time: 14:35
 */

if (!isset($_POST) || !isset($_POST['map'])) {
    exit();
}
require_once '../config/config.php';
$registry = Registry::getInstance();

$data = $_POST;

//Выбор обработчика запроса
switch ($data['map']) {
    //Убираем сообщение из сессии и памяти
    case 'closeEvent' :
        if (isset($data['typeEvent']) && $data['typeEvent'] != '') {
            Flash::closeEvent($data['typeEvent']);
            echo "ok";
        } else {
            return "error";
        }
        break;

//    case 'save_user' :
//        $perm = Permission::getPermission($registry);
//        if (!$perm->checkPerm('HomePage')) {
//            exit();
//        }
//        echo HomePage::ajaxIndex();
//        break;

    default:
        exit();
}