<?php
namespace crm;

/**
 * Класс для вывода ошибок
 * User: ADrushka
 * Date: 13.06.2017
 * Time: 23:19
 */

class ErrorMessage
{
    private static $error = [
        0 => "Неизвестная ошибка",
        1 => "Ошибка подключения к базе данных",
        2 => "<br /><br /><br /><br /><br /><h4 align='center'>У Вас недостаточно прав для просмотра данной страницы</h4style>",
        'rem_subject' => "Вы забыли свой пароль?",
        'rem_message' => "Для восстановления доступа, обратитесь в службу поддержки",
        'empty_data_auth' => '<label class="alert-danger">Проверьте правильность email и пароля</label>',
        'nevalid_email_auth' => '<label class="alert-danger">Проверьте правильность написания email</label>',
        'nevalid_pass_auth' => '<label class="alert-danger">Введите верный пароль</label>',
        'nevalid_pass_reg' => '<label class="alert-danger">Введённые пароли не совпадают</label>',
        404 => "<br /><br /><br /><br /><br /><h1 align='center'>404 Страница не существует</h1>",


    ];

    /**
     * Вывод сообщения по id
     * id может быть строкой или числом - ключ массива $error,
     * который описан выше
     *
     * @param $id
     * @return mixed
     */
    public static function text($id)
    {
        if (isset($id) && isset(self::$error[$id])) {
            return self::$error[$id];
        }
        return self::$error[0];
    }

    /**
     * Dev - функция для быстрого вывода сообщения в <pre>
     * @param $id
     */
    public static function flashText($id)
    {
        if (isset($id) && isset(self::$error[$id])) {
            echo "<pre>";
            echo self::$error[$id];
            echo "</pre>";
        }
        echo "<pre>";
        echo self::$error[0];
        echo "</pre>";
    }

}