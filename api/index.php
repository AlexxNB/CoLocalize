<?php
require_once('../classes/class_restapi.php');
$api = new Restapi();

$group = $api->getGroup();

if(!$group) $api->clientError('Не указана группа');
if(!file_exists($group.'.php')) $api->clientError('Неверная группа');

require_once("$group.php");

$api->clientError('Неизвестная ошибка');
?>
