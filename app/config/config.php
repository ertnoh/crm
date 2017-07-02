<?php
/**
 * Базовый конфиг системы
 * CRM
 * @author Друшка Александр <fenix-site@yandex.ru>
 */
//Пляски с языком для авторизации
//TODO избавиться от дополнительного языкового конфига
require_once "lang.php";
mb_internal_encoding('UTF-8');

//Язык
define("SYSTEM_LANG", LANG);


//Основные папки
define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
define('SITE_FOLDER', ROOT_FOLDER . 'app/');
define('CONFIG_FOLDER', SITE_FOLDER . 'config/');
define("SYSTEM_LANG_FOLDER", SITE_FOLDER."system/lang/".SYSTEM_LANG."/");

//логирование
define('LOG_FOLDER', SITE_FOLDER . 'logs/');
define('ERROR_LOG', LOG_FOLDER . 'error_log.txt');
define('COMMON_LOG', LOG_FOLDER . 'common_log.txt');

//ядро системы
define('CLASSES_FOLDER', SITE_FOLDER . 'system/classes/');
define('KERNEL_FOLDER', SITE_FOLDER . 'system/kernel/');

//Контроллеры, модели, вьюшки
define('CONTROLLER_FOLDER', SITE_FOLDER . 'controller/');
define('MODEL_FOLDER', SITE_FOLDER . 'model/');
define('VIEW_FOLDER', SITE_FOLDER . 'view/');
define('AJAX', SITE_FOLDER . 'ajax/');

// Тип инстанса - разработческий, боевой, тестовый.
// Может принимать значения "dev", "prod", "test".
// На боевой системе всегда должна иметь значение "prod".
// В зависимости от нее можно устанавливать уровни логирования,
// показа ошибок и т.п.
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', "dev");
}

// Показ ошибок включен только на разработческих инстансах
ini_set('display_errors', (ENVIRONMENT === "dev") ? 'on' : 'off');
ini_set('log_errors', 'on');

//База данных по умолчанию
define('DB', 'LiteDB');
(ENVIRONMENT === "dev") ? define('USERNAME', '') : define('USERNAME', 'пользователь для боевой базы');
(ENVIRONMENT === "dev") ? define('PASS', '') : define('PASS', 'пароль для боевой базы');
(ENVIRONMENT === "dev") ? define('DBHOST', 'localhost:27017') : define('HOST', 'localhost:27017');

//Автолоадер - подгружает классы
//==============================================================================
require_once '/../Autoloader.php';
$loader = new crm\Autoloader();
$loader->register();
$loader->addPrefix('crm', CLASSES_FOLDER);
$loader->addPrefix('crm', KERNEL_FOLDER);
$loader->addPrefix('crm', CONTROLLER_FOLDER);
$loader->addPrefix('crm', MODEL_FOLDER);
$loader->addPrefix('crm', SYSTEM_LANG_FOLDER);
//$loader->addPrefix('crm', AJAX);
//==============================================================================
