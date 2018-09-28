<?php
/**
 * Created by PhpStorm.
 * User: gurik
 * Date: 27.09.2018
 * Time: 21:57
 */



namespace main;

include_once(__DIR__.'/const.php');
include_once(__DIR__.'/DBConnect.php');
include_once(__DIR__.'/Api.php');
include_once(__DIR__.'/FrontController.php');


use db\Api;
use \FrontController;

/* Инициализация и запуск FrontController */
$front = FrontController::getInstance();
$front->route();
//$test = new Api();
//echo $test->Table('News')."<hr>";
//echo $test->Table('Session')."<hr>";
//var_dump(json_decode($test->SessionSubscribe(1,'airmail@petshop.ru')));
?>

<div id="table">
    <form action="/Api/table" method="POST">
        <label for="table">Название таблицы</label>
        <input id="table" name="table" type="text" value=""/>
        <input type="submit" value="Посмотреть данные таблицы"/>
    </form>
</div>

<div id="subscribe">
    <form action="/Api/table" method="POST">
        <label for="email">Название таблицы</label>
        <input id="email" name="email" type="text" value=""/>
        <input type="submit" value="Записаться на курс"/>
    </form>
</div>



