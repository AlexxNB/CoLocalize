<?php

require_once("classes/class_page.php");
require_once("classes/class_auth.php");

$page = new Page();
$auth = new Auth();

if($page->GetURL(2)=='logout'){
    $auth->Logout();
    $page->Location('/login');
}

if($auth->IsAuthed()) $page->Location('/');

$page->AddJSLink('/res/js/login.js');


$page->Title = $page->L['login:title'];

$loginForm = $page->View('login_form');


$page->Content = $loginForm->HTML();
$page->makePage();