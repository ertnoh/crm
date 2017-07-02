<?php
namespace crm;

session_start();
if(!isset($_SESSION['user'])){
    if(isset($_POST) && !empty($_POST)){
        require_once 'app/auth/auth.php';
        exit();
    }
    require_once 'app/auth/auth_form.php';
    exit();
}
//$time = microtime(true);

require_once 'app/config/config.php';

//Запускаем регистр
$registry = Registry::getInstance();

# Создаём объект шаблонов
$template = new Template($registry);
$registry->set('template', $template);

# Загружаем router
$router = new Router($registry);
$registry->set('router', $router);

//Модели подгружаются прямо
//в контроллере по мере необходимости
$router->delegate();



//$time2 = microtime(true);
//echo ($time2 - $time)." сек";
//echo '<br>Памяти использовано: ',round(memory_get_usage()/1024/1024,2),' MB';
//echo '<br>Пиковая нагрузка на память ',round(memory_get_peak_usage()/1024/1024,2),' MB';