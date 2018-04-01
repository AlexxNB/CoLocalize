<?php
require_once("class_db.php");
class Projects{
    public function GetUserProjects($userid){
        $db = new DB();
        if(!$list = $db->SelectInArray('projects_users',"userid=$userid")) return false;
        $prList=array();
        foreach($list as $line){
            if(!$p = $this->GetProject($line['projectid'])) continue;
            $prList[]=$p;
        }
        return $prList;
    }

    public function GetProject($projectid){
        $db = new DB();
        if(!$projectDB = $db->SelectRow('projects',"id=$projectid")) return false;
        return $this->_makeProjectObj($projectDB);
    }

    public function GetProjectByCode($code){
        $db = new DB();
        if(!$project = $db->SelectRow('projects',"public_link='$code'")) return false;
        return $this->_makeProjectObj($projectDB);
    }

    public function CreateProject($title,$descr,$creator){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr,
            "is_public"=>0,
            "public_link"=>'',
            "creator"=>$creator
        );
        if(!$pid = $db->Insert('projects',$prop)) return false;
        $prop["id"] = $pid;
        return $this->_makeProjectObj($prop);
    }

    private function _makeProjectObj($data){
        $project = new Project();
        $project->ID = $data['id'];
        $project->Title = $data['title'];
        $project->Descr = $data['descr'];
        $project->IsPublic = ($data['is_public']==1) ? true : false;
        $project->PublicLink = $data['public_link'];
        $project->Creator = $data['creator'];

        return $project;
    }
}

class Project{
    public $ID;
    public $Title;
    public $Descr;
    public $IsPublic;
    public $PublicLink;
    public $Creator;

    public function SaveProject($title,$descr){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr
        );
        return $db->Update('projects',$prop,"id=$this->ID");
    }

    public function GetUserRole($userid){
        $db = new DB();
        return $db->SelectCell('projects_users','role',"projectid=$this->ID AND userid=$userid");
    }

    public function SetUserRole($userid,$role){
        $db = new DB();
        if($this->GetUserRole($userid)){
            $db->Update('projects_users',array('role'=>$role),"projectid=$this->ID AND userid=$userid");
        }else{
            $prop = array(
                "projectid"=>$this->ID,
                "userid"=>$userid,
                "role"=>$role
            );
            $db->Insert('projects_users',$prop);
        }
    }

    public function CheckUserRole($userid,$role){
        $cRole = $this->GetUserRole($userid);
        if($cRole == $role) return true;
        return false;
    }

    public function DeleteProject(){
        $db = new DB();
        if(!$db->Delete('projects',"id=$this->ID")) return false;
        if(!$db->Delete('projects_users',"projectid=$this->ID")) return false;
        return true;
    }

    public function MakePublic($code){
        $db = new DB();
        $prop = array(
            'is_public'=>1,
            'public_link'=>$code
        );
        $db->Update('projects',$prop,"id=$this->ID");
    }

    public function MakePrivate(){
        $db = new DB();
        $prop = array(
            'is_public'=>0
        );
        $db->Update('projects',$prop,"id=$this->ID");
    }
}
?>