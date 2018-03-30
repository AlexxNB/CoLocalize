<?php
require_once("class_db.php");
class Projects{
    public function GetUserProjects($userid){
        $db = new DB();
        if(!$list = $db->SelectInArray('projects_users',"userid=$userid")) return false;
        $prList=array();
        foreach($list as $line){
            if(!$p = $this->GetProject($line['projectid'])) continue;
            $p['role'] = $line['role'];
            $prList[]=$p;
        }
        return $prList;
    }

    public function GetProject($projectid){
        $db = new DB();
        if(!$project = $db->SelectRow('projects',"id=$projectid")) return false;
        return $project;
    }

    public function GetProjectByCode($code){
        $db = new DB();
        if(!$project = $db->SelectRow('projects',"public_link='$code'")) return false;
        return $project;
    }

    public function CreateProject($title,$descr,$creator){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr,
            "creator"=>$creator
        );
        return $db->Insert('projects',$prop);
    }

    public function MakePublic($projectid,$code){
        $db = new DB();
        $prop = array(
            'is_public'=>1,
            'public_link'=>$code
        );
        $db->Update('projects',$prop,"id=$projectid");
    }

    public function GetUserRole($projectid,$userid){
        $db = new DB();
        return $db->SelectCell('projects_users','role',"projectid=$projectid AND userid=$userid");
    }

    public function SetUserRole($projectid,$userid,$role){
        $db = new DB();
        if($this->GetUserRole($projectid,$userid)){
            $db->Update('projects_users',array('role'=>$role),"projectid=$projectid AND userid=$userid");
        }else{
            $prop = array(
                "projectid"=>$projectid,
                "userid"=>$userid,
                "role"=>$role
            );
            $db->Insert('projects_users',$prop);
        }
    }
}
?>