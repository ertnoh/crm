<?php
namespace crm;

use crm\Connect;
use crm\ErrorMessage;

/**
 * В этом модуле происходит
 * Валидация данных (ошибки выводятся через сессию)
 * Авторизация
 * Регистрация
 * Забыл пароль
 *
 */


//Проверка данных
if (!is_valid($_POST)) {
    $_POST = [];
    header('Location: /');
    exit();
}

$data_post = $_POST;
//Ещё одна простая валидация
foreach ($data_post as $key => $val) {
    $data[$key] = strip_tags(htmlentities($val, ENT_QUOTES));
}

//Авторизация
//=======================================================================
if (isset($data['loginEmail']) && isset($data['loginPass'])) {
    //Соединяемся с базой
    require_once 'app/config/config.php';
    $conn = new Connect();
    $conn->selectTable('users');
    $user = $conn->findOneArr([
        'email' => $data['loginEmail'],
        'pass' => md5($data['loginPass']),
    ]);

    if (isset($user) && !empty($user) && !isset($_SESSION['user'])) {
        $hash = sha1(microtime());
        $_SESSION['user'] = $hash;
        $conn->updateOne(['email' => $data['loginEmail'], 'pass' => md5($data['loginPass'])],
            ['$set' => ['_sess_hash' => $hash]]);

        header('Location: /');
        exit();
    } else {
        header('Location: /');
        exit();
    }
}

//Регистрация
//=======================================================================
if (isset($data['regEmail']) && isset($data['regPass'])) {
    //Соединяемся с базой
    require_once 'app/config/config.php';
    $conn = new Connect();
    $conn->selectTable('users');
    $conn->table = 'users';
    $hash = sha1(microtime());
    $newuser = [
        'login' => $data['regEmail'],
        'email' => $data['regEmail'],
        'pass' => md5($data['regPass']),
        'role' => 2,
        '_sess_hash' => $hash,
    ];

    $email = $data['regEmail'];

    $olduser = $conn->findOneArr(['email' => $email]);
    if(!empty($olduser)){
        header('Location: /');
        exit();
    }
    $_SESSION['user'] = $hash;
    $conn->insert($newuser);
    header('Location: /');
    exit();
}

//Забыл пароль
//=======================================================================
if(isset($data['remEmail'])){
    //Соединяемся с базой
    require_once 'app/config/config.php';
    $conn = new Connect();
    $conn->selectTable('users');

    $user = $conn->findOneArr([
        'email' => $data['remEmail'],
    ]);

    if (isset($user) && !empty($user) && !isset($_SESSION['user'])) {
        $message = ErrorMessage::text('rem_message');
        mail($data['remEmail'], ErrorMessage::text('rem_subject'), $message);
        header('Location: /');
        exit();
    }
    else {
        header('Location: /');
        exit();
    }
}

//Какой-то странный вариант - если всё таки ничего не отработало
header('Location: /');
exit();

//=======================================================================



/**
 * Проверяет валидность логина- пароля
 *
 * @param null $data
 * @return bool
 */
function is_valid($data = null)
{
    require_once 'app/config/lang.php';
    require_once '/app/system/lang/'.LANG.'/ErrorMessage.php';
    if (!isset($data) || empty($data) || !is_array($data)) {
        error(ErrorMessage::text("empty_data_auth"), "all");
        return false;
    }

    if (isset($data['loginEmail']) && $data['loginEmail'] == ''
        || isset($data['loginEmail']) &&
        !filter_var($data['loginEmail'], FILTER_VALIDATE_EMAIL)
    ) {
        error(ErrorMessage::text("nevalid_email_auth"), "email_error");
        return false;
    }

    if (isset($data['loginPass']) && $data['loginPass'] == '') {
        error(ErrorMessage::text("nevalid_pass_auth"), "pass_error");
        return false;
    }

    if (isset($data['regEmail']) && $data['regEmail'] == ''
        || isset($data['regEmail']) &&
        !filter_var($data['regEmail'], FILTER_VALIDATE_EMAIL)
    ) {
        error(ErrorMessage::text("nevalid_email_auth"), "email_error");
        return false;
    }

    if (isset($data['regPass']) && $data['regPass'] == '') {
        error(ErrorMessage::text("nevalid_pass_auth"), "pass_error");
        return false;
    }

    if (isset($data['regPass2']) && $data['regPass2'] == '') {
        error(ErrorMessage::text("nevalid_pass_auth"), "pass_error");
        return false;
    }

    if (isset($data['regPass']) && isset($data['regPass2']) &&
        $data['regPass'] != $data['regPass2']
    ) {
        error(ErrorMessage::text("nevalid_pass_auth"), "pass2_error");
        return false;
    }

    if (isset($data['remEmail']) && $data['remEmail'] == ''
        || isset($data['remEmail']) &&
        !filter_var($data['remEmail'], FILTER_VALIDATE_EMAIL)
    ) {
        error(ErrorMessage::text("nevalid_pass_reg"), "pass_error");
        return false;
    }

    return true;
}


/**
 * Простая функция для сохранения ошибок валидации в сессии,
 * для вывода после перезагрузки страницы
 *
 * @param $text
 * @param $key
 * @return bool
 */
function error($text, $key){
    session_start();
    if(isset($text) && !empty($text) && isset($key) && !empty($key)){
        $_SESSION[$key] = $text;
    }
    return true;
}