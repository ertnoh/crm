<?php
/**
 * Контроллер для вывода специальных страниц вроде 404 и других
 *
 * Вообще говоря данные страницы перенаправляются через .htaccess
 * вот таким образом: ErrorDocument 404 /page404.php
 * Но сейчас нет времени делать такую страницу.
 *
 * Здесь будет просто такой псевдометод, для поддержки ООП
 * и одинакового перенаправления из всех мест кода с сообщениями.
 * Вполне можно было бы обойтись и без этого контроллера.
 * TODO доделать перенаправление как положено, создать страницы перенаправления
 *
 * User: ADrushka
 * Date: 15.06.2017
 * Time: 17:27
 */

namespace crm;

use crm\Controller;
use crm\ErrorMessage;


class SpecialPage extends Controller
{
    public function index()
    {
        $this::redirect404();
    }

    /**
     * Редирект на 404 страницу
     *
     * @param $message
     */
    public static function redirect404($message = '')
    {
        if ($message == '') {
            $message = ErrorMessage::text(404);
        }
        header("HTTP/1.0 404 Not Found");
        exit($message);
    }

    public static function redirectMain(){
        header('Location: /');
        exit();
    }
}