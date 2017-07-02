<?php
/**
 * Класс для вывода мгновенных сообщений
 * Он расширяет возможности класса Event
 *
 * 1. Реализует вывод сообщений и сообщений об ошибках до перезагрузки страницы
 * без сохранении где-либо message, error_message
 * 2. Реализует системные сообщения, которые исчезнут только после закрытия через ajax
 * с сохранением их в сессии systemNotice
 *
 * User: ADrushka
 * Date: 21.06.2017
 * Time: 16:12
 */

namespace crm;

use crm\Event;
use crm\Registry;

class Flash
{

    /**
     * Выводит текст в какую -то переменную шаблона
     * в виде сообщения в оформлении дивов, которое нигде не сохраняется.
     * В шаблоне достаточно указать вывод этой переменной
     * Если переменная не указана, выводит сообшение в переменной flash
     *
     * @param $text
     * @param string $template_variable
     * @return bool
     */
    public static function message($text, $template_variable = 'flash')
    {
        if (isset($text) && $text != '') {
            $text = '<div class="alert alert-info alert-dismissable">'
                . '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
                . $text
                . '</div>';

            $registry = Registry::getInstance();
            if (isset($registry['template'])) {
                $registry['template']->set($template_variable, $text);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }



    /**
     * Выводит ошибку в какую -то переменную шаблона
     * в виде сообщения в оформлении дивов, которое нигде не сохраняется.
     * В шаблоне достаточно указать вывод этой переменной
     * Если переменная не указана, выводит сообшение в переменной error
     *
     * @param $text
     * @param string $template_variable
     * @return bool
     */
    public static function error_message($text, $template_variable = 'error')
    {
        if (isset($text) && $text != '') {
            $text = '<div class="alert alert-info alert-dismissable">'
                . '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
                . $text
                . '</div>';

            $registry = Registry::getInstance();
            if (isset($registry['template'])) {
                $registry['template']->set($template_variable, $text);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }



    /**
     * Гарантировано выводит текст вверху страницы до основного вывода
     * (до формирования самой страницы)
     * вне зависимости от перезагрузки страницы, благодаря сохранению сообщения
     * в сессии
     *
     * Если введён текст, выводит его в тэгах <div ...>, вверху страницы
     * Для того, чтобы его убрать, нужно будет его в обязательном
     * порядке закрыть сообщение,
     * после чего он удалится из сессии через ajax - closeEvent($typeEvent)
     *
     * Если текста нет, выводит старое сообщение, сохранённое в сессии
     *
     * @param string $text
     */
    public static function systemNotice($text = '')
    {
        $event = Event::getInstance();
        $flash = $event->getEvent('perm_flash');
        if ((!$flash || $flash != $text) && $text != '') {
            $event->setEvent('perm_flash', $text);
            $flash = $event->getEvent('perm_flash');
        }
        if(!empty($flash)){
            echo '<div class="alert alert-info alert-dismissable">';
            echo '<a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="closeEvent(&quot;perm_flash&quot;)">&times;</a>';
            echo $flash;
            echo '</div>';
        }
    }

    public static function getMessage($key){
        $event = Event::getInstance();
        return $event->getEvent($key);
    }

    /**
     * Удаление сообщений для тех, которые были сохранены в сессии
     *
     * @param $typeEvent
     */
    public static function closeEvent($typeEvent)
    {
        $message = Event::getInstance();
        $message->removeEvent($typeEvent);
    }


}