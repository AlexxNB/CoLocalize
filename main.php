<?php

require_once("classes/class_page.php");

$page = new Page();

$page->Title = 'Hello';

$Content = $page->View('main');
$page->Content = $Content->HTML();

$page->makePage();