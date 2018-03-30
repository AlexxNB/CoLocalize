<?php
require_once('../classes/class_restapi.php');
require_once('../classes/class_auth.php');
require_once('../classes/class_page.php');
require_once('../classes/class_projects.php');
require_once('../classes/class_utils.php');
$api = new Restapi();
$auth = new Auth();
$page = new Page();
$prj = new Projects();
$utils = new Utils();

$command = $api->getCommand();
$L = $page->L;

//Get Public Link For The Project
if($api->getCommand() == 'getPublicLink'){
	$pid = $api->getParam('pid');
	$code = uniqid();

	if(!empty($pid))
		if($project = $prj->GetProject($pid))
			if(!empty($project['public_link'])){
				$code = $project['public_link'];
			}
		
	$url = $utils->GetHostURL().'/projects/p/'.$code;
	$api->makeJSON($url);			
}



//User register
if($api->getCommand() == 'add'){
	$name = $api->getParam('name');
	$email = $api->getParam('email');
	$password = $api->getParam('password');
	$password2 = $api->getParam('password2');

	if(empty($name)) 								    $api->clientError($L['login:signup:msg:empty_name'],'name');
	if(empty($email)) 								    $api->clientError($L['login:signup:msg:empty_email'],'email');
	if(empty($password)) 							    $api->clientError($L['login:signup:msg:empty_password'],'password');
	if(empty($password2)) 							    $api->clientError($L['login:signup:msg:empty_password2'],'password2');

	if(mb_strlen($name,"UTF-8") < 2) 					$api->clientError($L['login:signup:msg:name_too_short'],'name');
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))		$api->clientError($L['login:signup:msg:email_fail'],'email');

	if($password != $password2)							$api->clientError($L['login:signup:msg:password_mismatch'],'password2');

	if($auth->IsEmail($email))							$api->serverError($L['login:signup:msg:email_exists'],'email');

	if(!$uid = $auth->Register($email,$password,$name))	$api->serverError($L['system_error']);
	$auth->AddPriv($uid,'contributor');
	$api->makeJSON('success');
}

// Авторизация пользователя
if($api->getCommand() == 'signin'){
	$email = $api->getParam('email');
	$password = $api->getParam('password');
	$remember = ($api->getParam('remember') == 1) ? true : false;

	if(empty($email)) 								    $api->clientError($L['login:signin:msg:empty_email'],'email');
    if(empty($password)) 							    $api->clientError($L['login:signin:msg:empty_password'], 'password');
    
    if(!$auth->IsEmail($email))                         $api->serverError($L['login:signin:msg:email_not_exists'],'email');
	if(!$auth->Login($email,$password,$remember)) 	    $api->serverError($L['login:signin:msg:wrong_password'], 'password');

	$api->makeJSON('success');
}

$api->clientError('Неизвестная команда');
?>