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
	$resp = array(
		'url'=>$url,
		'code'=>$code
	);
	$api->makeJSON($resp);			
}



//Create new project
if($api->getCommand() == 'add'){
	if(!$user = $auth->GetUser()) $api->serverError($L['auth_require']);

	$title = $api->getParam('title');
	$descr = $api->getParam('descr');
	$isPublic = ($api->getParam('isPublic') == 1) ? true : false;
	$pubLinkCode = $api->getParam('pubLinkCode');

	if(empty($title)) 								    $api->clientError($L['projects:form:msg:empty_title'],'title');
	if(empty($descr)) 								    $api->clientError($L['projects:form:msg:empty_descr'],'descr');
	if($isPublic && empty($descr)) 					    $api->clientError($L['projects:form:msg:empty_publink'],'pubLink');

	if(strlen($title) > 128)							$api->clientError($L['projects:form:msg:long_title'],'title');
	if(strlen($descr) > 255)							$api->clientError($L['projects:form:msg:long_descr'],'descr');

	if($prj->GetProjectByCode($pubLinkCode))			$api->serverError($L['projects:form:msg:dub_publink'],'pubLink');


	if(!$pid = $prj->CreateProject($title,$descr,$user['id']))	$api->serverError($L['system_error']);
	$prj->MakePublic($pid,$pubLinkCode);
	$prj->SetUserRole($pid,$user['id'],'admin');
	
	$api->makeJSON($pid);
}

$api->clientError('Неизвестная команда');
?>