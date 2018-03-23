<?php

require_once("classes/class_page.php");
require_once("classes/class_language.php");

$lang = new Language();
$l = $lang->GetLangVars('en');
var_dump($l);

exit();

$page = new Page();

$page->Title = 'Hello';

$Content = $page->View('main');
$page->Content = $Content->HTML();

$page->makePage();