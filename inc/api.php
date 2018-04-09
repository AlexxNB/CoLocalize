<?php
require_once('classes/class_restapi.php');
$api = new Restapi();

$group = $api->getGroup();

if(!$group) $api->clientError('Empty group');
if(!file_exists("handlers/$group.php")) $api->clientError('Unknown group');

require_once("handlers/$group.php");

$api->clientError('Unknown error');
?>
