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
if($api->getCommand() == 'add' || $api->getCommand() == 'save'){
	$save = ($api->getCommand() == 'save') ? true : false;
	if(!$user = $auth->GetUser()) $api->serverError($L['auth_require']);

	$pid = $api->getParam('pid');
	$title = $api->getParam('title');
	$descr = $api->getParam('descr');
	$isPublic = ($api->getParam('isPublic') == 1) ? true : false;
	$pubLinkCode = $api->getParam('pubLinkCode');

	if($save){
		if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
		if(!$prj->CheckUserRole($pid,$user['id'],'admin')) $api->serverError($L['auth_error']);
	}


	if(empty($title)) 								    $api->clientError($L['projects:form:msg:empty_title'],'title');
	if(empty($descr)) 								    $api->clientError($L['projects:form:msg:empty_descr'],'descr');
	if($isPublic && empty($pubLinkCode)) 			    $api->clientError($L['projects:form:msg:empty_publink'],'pubLink');

	if(strlen($title) > 128)							$api->clientError($L['projects:form:msg:long_title'],'title');
	if(strlen($descr) > 255)							$api->clientError($L['projects:form:msg:long_descr'],'descr');

	if(!$save && $isPublic && $prj->GetProjectByCode($pubLinkCode))			$api->serverError($L['projects:form:msg:dub_publink'],'pubLink');

	if($save){
		if(!$prj->SaveProject($pid,$title,$descr))		$api->serverError($L['system_error']);
		if($isPublic) {
			$prj->MakePublic($pid,$pubLinkCode);
		}else{
			$prj->MakePrivate($pid);
		}
	}else{
		if(!$pid = $prj->CreateProject($title,$descr,$user['id']))	$api->serverError($L['system_error']);
		$prj->SetUserRole($pid,$user['id'],'admin');
		if($isPublic) $prj->MakePublic($pid,$pubLinkCode);
	}

	$api->makeJSON($pid);
}

//Delete project
if($api->getCommand() == 'delete'){
	$pid = $api->getParam('pid');
	
	if(!$user = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$prj->CheckUserRole($pid,$user['id'],'admin')) $api->serverError($L['auth_error']);

	if(!$prj->DeleteProject($pid))	$api->serverError($L['system_error']);

	$api->makeJSON('success');
}

$api->clientError('Неизвестная команда');
?>