<?php

require_once("classes/class_page.php");
require_once("classes/class_auth.php");
$page = new Page();

if($page->GetURL(2)=='logout'){
    $auth = new Auth();
    $auth->Logout();
    $page->Location('/login');
}

$page->AddJSLink('/template/js/login.js');


$page->Title = 'Вход в систему!';
$page->OneColumn = true;

$loginForm = $page->View('login_form');


$page->Content = $loginForm->HTML();
$page->makePage();