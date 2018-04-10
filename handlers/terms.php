<?php
require_once('classes/class_restapi.php');
require_once('classes/class_language.php');
require_once('classes/class_parsers.php');
require_once('classes/class_auth.php');
require_once('classes/class_projects.php');
$api = new Restapi();
$auth = new Auth();
$lang = new Language();
$parsers = new Parsers();
$prj = new Projects();

$command = $api->getCommand();
$L = $lang->GetLangVars();

//Import file
if($api->getCommand() == 'importfile'){
	$pid = $api->getParam('pid');
	$parserid = $api->getParam('parser');

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'import_terms')) $api->serverError($L['auth_error']);
	
	if(!$Parser = $parsers->GetParser($parserid)) $api->serverError($L['system_error']);
	
	if (isset($_FILES['file']['name'])) {
		if ($_FILES['file']['error'] > 0) {
			$api->serverError($L['terms:import:msg:error_upload']);
		} else {
			$filename = $_FILES['file']['tmp_name'];
			if(!$content = file_get_contents($filename)) $api->serverError($L['terms:import:msg:parsing_error']);
			if(!$terms = $Parser->ImportTerms($content)) $api->serverError($L['terms:import:msg:parsing_error']);

			foreach($terms as $term){
				$Project->Terms->AddQueue($term);
			}
			
			$Project->Terms->SaveQueue();
			$api->makeJSON('success');
		}
	} else {
		$api->serverError($L['system_error']);
	}
}

//Load list by query
if($api->getCommand() == 'load'){
	$pid = $api->getParam('pid');
	$num = $api->getParam('num');
	$query = $api->getParam('query');

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'edit_terms')) $api->serverError($L['auth_error']);

	if(!$list = $Project->Terms->Find($query,$num,20)) $api->serverError($L['no_entries']);

	$api->makeJSON($list);
}

if($api->getCommand() == 'add'){
	$pid = $api->getParam('pid');
	$name = $api->getParam('name');

	if(empty($name)) $api->clientError($L['terms:view:msg:empty_name']);

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'edit_terms')) $api->serverError($L['auth_error']);

	$Project->Terms->AddTerm($name);
	$api->makeJSON($L['terms:view:msg:add_success']);
}

if($api->getCommand() == 'save'){
	$pid = $api->getParam('pid');
	$tid = $api->getParam('tid');
	$value = $api->getParam('value');

	if(empty($value)) $api->clientError($L['terms:view:msg:empty_value']);

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!preg_match('|^\d+$|',$tid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'edit_terms')) $api->serverError($L['auth_error']);

	$Project->Terms->SaveTerm($tid,$value);
	$api->makeJSON($L['terms:view:msg:save_success']);
}

if($api->getCommand() == 'delete'){
	$pid = $api->getParam('pid');
	$tid = $api->getParam('tid');

	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!preg_match('|^\d+$|',$tid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
	if(!$Project->CanUserDo($User,'edit_terms')) $api->serverError($L['auth_error']);

	$Project->Terms->DeleteTerm($tid);
	$api->makeJSON($L['terms:view:msg:delete_success']);
}

$api->clientError('Unknown command');
?>