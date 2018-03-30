<?php
require_once("classes/class_page.php");

$page = new Page();

$action = $page->GetURL(2);
if(!$action) $action = 'list';

if($action == 'list'){
    $page->Title = $page->L['projects:title'];
    $Content = $page->View('projects_list');
    $page->Content = $Content->HTML();
    $page->makePage();
}

if($action == 'add'){
    $page->AddJSLink('/res/js/project_form.js');
    
    $page->Title = $page->L['projects:add'];
    $Form = $page->View('project_edit_form');
    $page->Content = $Form->HTML();
    $page->makePage();
}
