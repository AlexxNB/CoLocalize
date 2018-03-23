<?php
require_once("classes/class_page.php");

$page = new Page();

$request = $page->GetURL(1);


if(!$request) $request='main';
if(!file_exists($request.'.php')) $page->show404();

require_once("$request.php");

?>
