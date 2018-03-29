<?php
header("HTTP/1.0 404 Not Found");
require_once("classes/class_page.php");


$page = new Page();
$page->OneColumn = true;


$page->Title = $page->L['404:title'];

$Content = $page->View('404');
$page->Content = $Content->HTML();

$page->makePage();