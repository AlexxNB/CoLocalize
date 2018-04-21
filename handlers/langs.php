<?php
require_once('classes/class_restapi.php');
require_once('classes/class_locales.php');
require_once('classes/class_parsers.php');
require_once('classes/class_auth.php');
require_once('classes/class_projects.php');
$api = new Restapi();
$auth = new Auth();
$local = new Locales();
$parsers = new Parsers();
$prj = new Projects();

$command = $api->getCommand();
$L = $local->GetLangVars();

if($api->getCommand() == 'getUnusedList'){
	$pid = $api->getParam('pid');
	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$list = $Project->Langs->GetUnusedList()) $api->serverError($L['system_error']);

	$api->makeJSON($list);
}

if($api->getCommand() == 'add'){
	$pid = $api->getParam('pid');
	$code = $api->getParam('code');

	if(empty($code)) $api->clientError($L['system_error']);

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'add_language')) $api->serverError($L['auth_error']);
	if(!$Lang = $Project->Langs->Info($code)) $api->serverError($L['langs:add:msg:no_lang']);
	if($Project->Langs->IsInList($code)) $api->serverError($L['langs:add:msg:in_list']);


	$Project->Langs->Add($code,$Lang,$User);
	$api->makeJSON($L['langs:add:msg:add_success']);
}
$api->clientError('Unknown command');
?>