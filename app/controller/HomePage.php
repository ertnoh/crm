<?php
namespace crm;

use crm\Controller;
use crm\Users;
use crm\Connect;
use crm\Flash;

/**
 * Контроллер главной страницы
 *
 * User: ADrushka
 * Date: 14.06.2017
 * Time: 2:01
 */
class HomePage extends Controller
{
    public $model_name = 'Users';

    public function index()
    {
        Flash::systemNotice();

        $this->registry['template']->set('title', "CRM Управление персоналом");
        $this->registry['template']->render('index');
    }

    public function logout(){
        session_start();
        if(isset($_SESSION['user'])){
            $this->getModel()->logout();
            unset($_SESSION['user']);
        }
        session_destroy();
        SpecialPage::redirectMain();
    }
}