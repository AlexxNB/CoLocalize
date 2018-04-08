<?php
require_once("class_db.php");
class Projects{
    public function GetUserProjects($User){
        $db = new DB();
        if(!$list = $db->SelectInArray('projects_users',"userid=$User->ID")) return false;
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

    public function CreateProject($title,$descr,$Creator){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr,
            "is_public"=>0,
            "public_link"=>'',
            "terms"=>0,
            "creator"=>$Creator->ID
        );
        if(!$pid = $db->Insert('projects',$prop)) return false;
        $prop["id"] = $pid;
        return $this->_makeProjectObj($prop);
    }

    private function _makeProjectObj($data){
        $project = new Project($data['id']);
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
    public $Terms;

    public function __construct($projectid=false){
        if($projectid) {
            $this->ID = $projectid;
            $this->Terms = new Terms($this);
        }
    }

    public function SaveProject($title,$descr){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr
        );
        return $db->Update('projects',$prop,"id=$this->ID");
    }

    public function GetUserRole($User){
        $db = new DB();
        return $db->SelectCell('projects_users','role',"projectid=$this->ID AND userid=$User->ID");
    }

    public function SetUserRole($User,$role){
        $db = new DB();
        if($this->GetUserRole($User)){
            $db->Update('projects_users',array('role'=>$role),"projectid=$this->ID AND userid=$User->ID");
        }else{
            $prop = array(
                "projectid"=>$this->ID,
                "userid"=>$User->ID,
                "role"=>$role
            );
            $db->Insert('projects_users',$prop);
        }
    }

    public function CheckUserRole($User,$role,$role2=false,$role3=false){
        if(!$cRole = $this->GetUserRole($User)) return false;
        if($cRole == $role) return true;
        if($cRole == $role2) return true;
        if($cRole == $role3) return true;
        return false;
    }

    public function CanUserDo($User,$action){
        switch($action){
            case 'view_project':
                if($this->CheckUserRole($User,'admin','contributor')) return true;
            break;
            
            case 'create_project':
            case 'delete_project':
            case 'edit_project':
            case 'import_terms':
            case 'edit_terms':
                if($this->CheckUserRole($User,'admin')) return true;
            break;

            default: 
                return false;
        }
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

class Terms{
    var $Project;
    var $TermsQueue;

    public function __construct($Project){
        $this->Project = $Project;
        $this->TermsQueue = array();
    }

    public function GetList(){
        $db = new DB();
        $terms = array();
        if(!$data = $db->SelectInArray('terms',"projectid=".$this->Project->ID)) return $terms;
        foreach($data as $line){
            $term = new stdClass();
            $term->ID = $line['id'];
            $term->Name = $line['term'];
            $terms[] = $term;
        }

        return $term;
    }

    public function Num(){
        $db = new DB();
        return $db->SelectNum('terms','projectid='.$this->Project->ID);
    }

    public function AddQueue($term){
        $this->TermsQueue[] = $term;
    }

    public function SaveQueue(){
        $db = new DB();
        $num = count($this->TermsQueue);
        if($num == 0) return false;
        
        $props = array();
        foreach($this->TermsQueue as $term){
            $props[] = array(
                'projectid'=>$this->Project->ID,
                'term'=>$term
            );
        }

        if(!$db->Insert('terms',$props,true)) return false;
    }
}
?>