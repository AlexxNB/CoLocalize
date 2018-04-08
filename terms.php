<?php
require_once("classes/class_page.php");
require_once("classes/class_auth.php");
require_once("classes/class_projects.php");
require_once("classes/class_parsers.php");

$page = new Page();
$auth = new Auth();
$prj = new Projects();
$parsers = new Parsers();

if(!$User = $auth->GetUser()) $page->Location('/login/');

$action = $page->GetURL(2);
if(!$action) $page->Location('/projects/');

if($action == 'import'){
    $pid = $page->GetURL(3);
    if(!$pid || !preg_match('|^\d+$|',$pid)) $page->Location('/projects/');
    if(!$Project = $prj->GetProject($pid)) $page->Location('/projects/');
    if(!$Project->CanUserDo($User,'import_terms')) $page->Location('/projects/');


    $page->AddJSLink('/res/js/terms_import.js');
    
    $page->Title = $page->L['terms:import:title'];
    $List = $page->View('terms_import');

    $List->Parsers = $parsers->GetParsersList();
    $List->Project= $Project;

    $page->Content = $List->HTML();
    $page->makePage();
}

if($action == 'view'){
    $pid = $page->GetURL(3);
    if(!$pid || !preg_match('|^\d+$|',$pid)) $page->Location('/projects/');
    if(!$Project = $prj->GetProject($pid)) $page->Location('/projects/');
    if(!$Project->CanUserDo($User,'edit_terms')) $page->Location('/projects/');

    $page->AddJSLink('/res/js/terms_view.js');
    
    $page->Title = $page->L['terms:view:title'];
    $List = $page->View('terms_view');

    $List->Project= $Project;

    $page->Content = $List->HTML();
    $page->makePage();
}
