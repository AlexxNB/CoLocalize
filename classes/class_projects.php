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
        $this->_decodeTypes($project);
        return $project;
    }

    public function GetProjectByCode($code){
        $db = new DB();
        if(!$project = $db->SelectRow('projects',"public_link='$code'")) return false;
        $this->_decodeTypes($project);
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

    public function SaveProject($projectid,$title,$descr){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr
        );
        return $db->Update('projects',$prop,"id=$projectid");
    }

    public function DeleteProject($projectid){
        $db = new DB();
        if(!$db->Delete('projects',"id=$projectid")) return false;
        if(!$db->Delete('projects_users',"projectid=$projectid")) return false;
        return true;
    }

    public function MakePublic($projectid,$code){
        $db = new DB();
        $prop = array(
            'is_public'=>1,
            'public_link'=>$code
        );
        $db->Update('projects',$prop,"id=$projectid");
    }

    public function MakePrivate($projectid){
        $db = new DB();
        $prop = array(
            'is_public'=>0
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

    public function CheckUserRole($projectid,$userid,$role){
        $cRole = $this->GetUserRole($projectid,$userid);
        if($cRole == $role) return true;
        return false;
    }

    private function _decodeTypes($project){
        if($project['is_public']==1) $project['is_public']=true;
        return $project;
    }
}
?>