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
    if(!$Project->CheckUserRole($User,'admin')) $page->Location('/projects/');


    $page->AddJSLink('/res/js/terms_import.js');
    
    $page->Title = $page->L['terms:import:title'];
    $List = $page->View('terms_import');

    $List->Parsers = $parsers->GetParsersList();
    $List->Pid= $Project->ID;

    $page->Content = $List->HTML();
    $page->makePage();
}
