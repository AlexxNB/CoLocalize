<?php
require_once("classes/class_page.php");
require_once("classes/class_auth.php");
require_once("classes/class_projects.php");

$page = new Page();
$auth = new Auth();
$prj = new Projects();

if(!$user = $auth->GetUser()) $page->Location('/login');

$action = $page->GetURL(2);
if(!$action) $action = 'list';

if($action == 'list'){
    $page->AddJSLink('/res/js/projects_list.js');
    
    $page->Title = $page->L['projects:title'];
    $List = $page->View('projects_list');

    if($projects = $prj->GetUserProjects($user['id'])){
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
    if(!$project = $prj->GetProject($pid)) $page->Location('/projects/');
    if(!$prj->CheckUserRole($pid,$user['id'],'admin')) $page->Location('/projects/');

    $page->AddJSLink('/res/js/project_form.js');
    
    $page->Title = $page->L['projects:edit'].': '.$project['title'];
    $Form = $page->View('project_edit_form');
    $Form->Project = $project;
    $page->Content = $Form->HTML();
    $page->makePage();
}
