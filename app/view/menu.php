<?php
?>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Навигация</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">CRM</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/">Главная</a></li>
                <li><a href="/users/details">Настройки</a></li>
                <li><a href="/users/profile">Профиль</a></li>
                <li><a href="/logout">Выйти</a></li>
            </ul>
            <form class="navbar-form navbar-right">
                <input type="text" class="form-control" placeholder="Поиск...">
            </form>
        </div>
    </div>
</div>
<div class="container-fluid" style="margin-top: 50px">
    <div class="row">
        <div class="col-sm-1 col-md-1 sidebar">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="/users"><span class="glyphicon glyphicon-user"></span></a></li>
                <li><a href="/chat"><span class="glyphicon glyphicon-tower"></span></a></li>
            </ul>
        </div>
    </div>
</div>