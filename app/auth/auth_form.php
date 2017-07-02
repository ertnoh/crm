<?php
/**
 * Форма авторизации.
 * Для удобства форма авторизации и обработчик в 1 папке
 *
 * User: ADrushka
 * Date: 20.06.2017
 * Time: 14:05
 */
session_start();
//Получаем ошибки
$all_error = $_SESSION['all_error'];
$email_error = $_SESSION['email_error'];
$pass_error = $_SESSION['pass_error'];
$pass2_error = $_SESSION['pass2_error'];
//Сразу удаляем их из сессии
unset($_SESSION['all_error']);
unset($_SESSION['email_error']);
unset($_SESSION['pass_error']);
unset($_SESSION['pass2_error']);
?>
<!DOCTYPE html>
<head xmlns="http://www.w3.org/1999/html">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="test">
    <meta name="author" content="CRM">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=0.5">
    <!-- jquery -->
    <script src="app/view/bootstrap/jquery.js"></script>
    <!-- bootstrap -->
    <link href="app/view/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="app/view/css/main.css" rel="stylesheet" type="text/css">
    <script src="app/view/bootstrap/js/bootstrap.min.js"></script>
    <title>Авторизация</title>

    <script>
        function show_reg_auth() {
            $("#auth_form").toggle();
            $("#reg_form").toggle();
            return false;
        }

        function show_auth_rem() {
            $("#auth_form").toggle();
            $("#rem_form").toggle();
            return false;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="auth_border">
                <div class="auth_div">
                    <form id="auth_form" class="form-horizontal" method="post" action="auth.php">
                        <fieldset>
                            <div class="form-group">
                                <label for="inputEmail" class="control-label col-xs-3">Email</label>
                                <div class="col-xs-3">
                                    <?=$email_error; ?>
                                    <input name="loginEmail" type="email" class="form-control" id="inputEmail"
                                           placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword" class="control-label col-xs-3">Пароль</label>
                                <div class="col-xs-3">
                                    <?=$pass_error; ?>
                                    <input name="loginPass" type="password" class="form-control" id="inputPassword"
                                           placeholder="Пароль">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <button type="submit" class="btn btn-primary">Войти</button>
                                    &nbsp;&nbsp;&nbsp;<a href="#" onclick="show_auth_rem()">Забыл пароль</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <a href="#" onclick="show_reg_auth()">Регистрация</a>

                                </div>
                            </div>

                        </fieldset>
                    </form>
                    <!-- Форма регистрации -->
                    <form id="reg_form" class="form-horizontal" style="display: none" method="post" action="auth.php">
                        <fieldset>
                            <div class="form-group">
                                <label for="inputEmail" class="control-label col-xs-3">Email</label>
                                <?=$email_error; ?>
                                <div class="col-xs-3">
                                    <input name="regEmail" type="email" class="form-control" id="inputEmail"
                                           placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword" class="control-label col-xs-3">Пароль</label>
                                <div class="col-xs-3">
                                    <?=$pass_error; ?>
                                    <input name="regPass" type="password" class="form-control" id="inputPassword"
                                           placeholder="Пароль">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword2" class="control-label col-xs-3">Повторите пароль</label>
                                <div class="col-xs-3">
                                    <?=$pass2_error; ?>
                                    <input name="regPass" type="password" class="form-control" id="inputPassword2"
                                           placeholder="Пароль">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <button type="submit" class="btn btn-primary">Зарегистрироваться!</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <a href="#" onclick="show_reg_auth()"> << Форма входа</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <!-- Забыл пароль -->
                    <form id="rem_form" class="form-horizontal" style="display: none" method="post" action="auth.php">
                        <fieldset>
                            <div class="form-group">
                                <label for="inputEmail" class="control-label col-xs-3">Email</label>
                                <div class="col-xs-3">
                                    <input name="remEmail" type="email" class="form-control" id="inputEmail"
                                           placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <button type="submit" class="btn btn-primary">Отправить</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-3">
                                    <a href="#" onclick="show_auth_rem()"> << Форма входа</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>