<?php
require_once("class_db.php");
class Projects{
    public function GetUserProjects($userid){
        $db = new DB();
        if($list = $db->SelectInArray('projects_contributors',"userid=$userid")) return false;
        $prList=array();
        foreach($list as $line){
            if(!$p = $this->GetProject($line['projectid'])) continue;
            $p['role'] = $line['role'];
            $prList[]=$p;
        }
    }

    public function GetProject($projectid){
        $db = new DB();
        if(!$project = $db->SelectRow('projects',"id=$projectid")) return false;
        return $project;
    }
}
?>