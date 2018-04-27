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

if($action == 'list'){
    $orignCode = $page->GetURL(3);
    $code = $page->GetURL(4);
    $lid = $page->GetURL(5);
    if(!$lid || !preg_match('|^\d+$|',$lid)) $page->Location('/projects/');
    if(!$orignCode || !preg_match('|^[a-zA-Z]+$|',$orignCode)) $page->Location('/projects/'); 
    if(!$code || !preg_match('|^[a-zA-Z]+$|',$code)) $page->Location('/projects/');
    if(!$Project = $prj->GetProjectByLangId($lid)) $page->Location('/projects/');
    if(!$Project->CanUserDo($User,'translate',$lid)) $page->Location('/projects/');

    if(!$Orign = $Project->Langs->Get($orignCode)) return false;
    if(!$Lang = $Project->Langs->Get($code)) return false;

    $page->AddJSLink('/res/js/translate.js');
    
    $page->Title = $page->L['translate:list:title'];
    $List = $page->View('translate_list');

    $List->Project= $Project;
    $List->Lang= $Lang;
    $List->Orign= $Orign;

    $page->Content = $List->HTML();
    $page->makePage();
}
