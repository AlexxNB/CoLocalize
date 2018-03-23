<?php

require_once("classes/class_page.php");


$page = new Page();
$page->OneColumn = true;


$page->Title = 'Страница не найдена!';
$page->Content = 'Запрошенной страницы не существует';


$page->makePage();