<?php
define('ROOT',$_SERVER['DOCUMENT_ROOT']);
set_include_path(ROOT);
require_once("classes/class_page.php");

$page = new Page();

$request = $page->GetURL(1);

if(!$request) $request='main';
if(!file_exists("inc/$request.php")) $request = '404';
require_once("inc/$request.php");

?>
