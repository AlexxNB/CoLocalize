<?php
require_once('../classes/class_restapi.php');
require_once('../classes/class_language.php');
require_once('../classes/class_parsers.php');
require_once('../classes/class_auth.php');
require_once('../classes/class_projects.php');
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
	if(!$Project->CheckUserRole($User,'admin')) $api->serverError($L['auth_error']);
	
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

$api->clientError('Unknown command');
?>