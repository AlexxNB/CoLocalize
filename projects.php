<?php
require_once("classes/class_page.php");
require_once("classes/class_auth.php");
require_once("classes/class_projects.php");

$page = new Page();
$auth = new Auth();
$prj = new Projects();

if(!$User = $auth->GetUser()) $page->Location('/login');

$action = $page->GetURL(2);
if(!$action) $action = 'list';

if($action == 'list'){
    $page->AddJSLink('/res/js/projects_list.js');
    
    $page->Title = $page->L['projects:title'];
    $List = $page->View('projects_list');

    if($projects = $prj->GetUserProjects($User)){
        $List->Projects = $projects;
    }


    $page->Content = $List->HTML();
    $page->makePage();
}

if($action == 'add'){
    $page->AddJSLink('/res/js/project_form.js');
    
    $page->Title = $page->L['projects:add'];
    $Form = $page->View('project_edit_form');
    $page->Content = $Form->HTML();
    $page->makePage();
}

if($action == 'edit'){
    $pid = $page->GetURL(3);
    if(!$pid || !preg_match('|^\d+$|',$pid)) $page->Location('/projects/');
    if(!$Project = $prj->GetProject($pid)) $page->Location('/projects/');
    if(!$Project->CanUserDo($User,'edit_project')) $page->Location('/projects/');

    $page->AddJSLink('/res/js/project_form.js');
    
    $page->Title = $page->L['projects:edit'].': '.$Project->Title;
    $Form = $page->View('project_edit_form');
    $Form->Project = $Project;
    $page->Content = $Form->HTML();
    $page->makePage();
}

if($action == 'view'){
    $pid = $page->GetURL(3);
    if(!$pid || !preg_match('|^\d+$|',$pid)) $page->Location('/projects/');
    if(!$Project = $prj->GetProject($pid)) $page->Location('/projects/');
    if(!$Project->CanUserDo($User,'view_project')) $page->Location('/projects/');

    $page->Title = $Project->Title;
    $Form = $page->View('project_view');
    $Form->Project = $Project;
    $Form->Termsnum = $Project->Terms->Num();
    $page->Content = $Form->HTML();
    $page->makePage();
}
