<?php
require_once('../classes/class_restapi.php');
require_once('../classes/class_auth.php');
require_once('../classes/class_page.php');
$api = new Restapi();
$auth = new Auth();
$page = new Page();

$command = $api->getCommand();
$L = $page->L;

//User register
if($api->getCommand() == 'signup'){
	$name = $api->getParam('name');
	$email = $api->getParam('email');
	$password = $api->getParam('password',false);
	$password2 = $api->getParam('password2',false);

	if(empty($name)) 								    $api->clientError($L['login:signup:msg:empty_name'],'name');
	if(empty($email)) 								    $api->clientError($L['login:signup:msg:empty_email'],'email');
	if(empty($password)) 							    $api->clientError($L['login:signup:msg:empty_password'],'password');
	if(empty($password2)) 							    $api->clientError($L['login:signup:msg:empty_password2'],'password2');

	if(mb_strlen($name,"UTF-8") < 2) 					$api->clientError($L['login:signup:msg:name_too_short'],'name');
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))		$api->clientError($L['login:signup:msg:email_fail'],'email');

	if($password != $password2)							$api->clientError($L['login:signup:msg:password_mismatch'],'password2');

	if($auth->IsEmail($email))							$api->serverError($L['login:signup:msg:email_exists'],'email');

	if(!$User = $auth->Register($email,$password,$name))	$api->serverError($L['system_error']);
	$User->AddPriv('contributor');
	$api->makeJSON('success');
}

// Авторизация пользователя
if($api->getCommand() == 'signin'){
	$email = $api->getParam('email');
	$password = $api->getParam('password',false);
	$remember = ($api->getParam('remember') == 1) ? true : false;

	if(empty($email)) 								    $api->clientError($L['login:signin:msg:empty_email'],'email');
    if(empty($password)) 							    $api->clientError($L['login:signin:msg:empty_password'], 'password');
    
    if(!$auth->IsEmail($email))                         $api->serverError($L['login:signin:msg:email_not_exists'],'email');
	if(!$auth->Login($email,$password,$remember)) 	    $api->serverError($L['login:signin:msg:wrong_password'], 'password');

	$api->makeJSON('success');
}

$api->clientError('Неизвестная команда');
?>