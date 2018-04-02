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
		if($Project = $prj->GetProject($pid))
			if(!empty($Project->PublicLink)){
				$code = $Project->PublicLink;
			}
		
	$url = $utils->GetHostURL().'/projects/p/'.$code;
	$resp = array(
		'url'=>$url,
		'code'=>$code
	);
	$api->makeJSON($resp);			
}



//Create new project or save one
if($api->getCommand() == 'add' || $api->getCommand() == 'save'){
	$save = ($api->getCommand() == 'save') ? true : false;
	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	$pid = $api->getParam('pid');
	$title = $api->getParam('title');
	$descr = $api->getParam('descr');
	$isPublic = ($api->getParam('isPublic') == 1) ? true : false;
	$pubLinkCode = $api->getParam('pubLinkCode');

	if($save){
		if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
		if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);
		if(!$Project->CheckUserRole($User,'admin')) $api->serverError($L['auth_error']);
	}


	if(empty($title)) 								    $api->clientError($L['projects:form:msg:empty_title'],'title');
	if(empty($descr)) 								    $api->clientError($L['projects:form:msg:empty_descr'],'descr');
	if($isPublic && empty($pubLinkCode)) 			    $api->clientError($L['projects:form:msg:empty_publink'],'pubLink');

	if(strlen($title) > 128)							$api->clientError($L['projects:form:msg:long_title'],'title');
	if(strlen($descr) > 255)							$api->clientError($L['projects:form:msg:long_descr'],'descr');

	if(!$save && $isPublic && $prj->GetProjectByCode($pubLinkCode))			$api->serverError($L['projects:form:msg:dub_publink'],'pubLink');

	if($save){
		if(!$Project->SaveProject($title,$descr))		$api->serverError($L['system_error']);
		if($isPublic) {
			$Project->MakePublic($pubLinkCode);
		}else{
			$Project->MakePrivate();
		}
	}else{
		if(!$Project = $prj->CreateProject($title,$descr,$User))	$api->serverError($L['system_error']);
		$Project->SetUserRole($User,'admin');
		if($isPublic) $Project->MakePublic($pubLinkCode);
	}

	$api->makeJSON($Project->ID);
}

//Delete project
if($api->getCommand() == 'delete'){
	$pid = $api->getParam('pid');
	
	if(!$User = $auth->GetUser()) $api->serverError($L['auth_require']);

	if(!preg_match('|^\d+$|',$pid))	$api->clientError($L['system_error']);
	if(!$Project = $prj->GetProject($pid)) $api->serverError($L['system_error']);

	if(!$Project->CheckUserRole($User,'admin')) $api->serverError($L['auth_error']);

	if(!$Project->DeleteProject())	$api->serverError($L['system_error']);

	$api->makeJSON('success');
}

$api->clientError('Неизвестная команда');
?>