<?php
require_once("class_db.php");
class Projects{
    public function GetUserProjects($User){
        $db = new DB();
        if(!$list = $db->GetArray("SELECT :n FROM :n WHERE :n=:d",'projectid','projects_users','userid',$User->ID)) return false;
        $prList=array();
        foreach($list as $line){
            if(!$p = $this->GetProject($line->projectid)) continue;
            $prList[]=$p;
        }
        return $prList;
    }

    public function GetProject($projectid){
        $db = new DB();
        if(!$ProjectDB = $db->GetRow("SELECT * FROM :n WHERE :n=:d",'projects','id',$projectid)) return false;
        return $this->_makeProjectObj($ProjectDB);
    }

    public function GetProjectByCode($code){
        $db = new DB();
        if(!$ProjectDB = $db->GetRow("SELECT * FROM :n WHERE :n=:s",'projects','public_link',$code)) return false;
        return $this->_makeProjectObj($ProjectDB);
    }

    public function CreateProject($title,$descr,$Creator){
        $db = new DB();
        $prop = array(
            "title"=>$title,
            "descr"=>$descr,
            "is_public"=>false,
            "public_link"=>'',
            "creator"=>$Creator->ID
        );
        $db->Query("INSERT INTO :n :i",'projects',$prop);
        $prop["id"] = $db->LastID();
        return $this->_makeProjectObj($prop);
    }

    private function _makeProjectObj($dataDB){
        if(is_array($dataDB)) $dataDB = json_decode(json_encode($dataDB), FALSE);
        
        $project = new Project($dataDB->id);
        $project->Title = $dataDB->title;
        $project->Descr = $dataDB->descr;
        $project->IsPublic = ($dataDB->is_public) ? true : false;
        $project->PublicLink = $dataDB->public_link;
        $project->Creator = $dataDB->creator;

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
        return $db->Query("UPDATE :n :u WHERE :n=:d",'projects',$prop,'id',$this->ID);
    }

    public function GetUserRole($User){
        $db = new DB();
        return $db->GetCell("SELECT :n FROM :n WHERE :n=:d AND :n=:d",
                                'role','projects_users',
                                'projectid',$this->ID,
                                'userid',$User->ID
                            );
    }

    public function SetUserRole($User,$role){
        $db = new DB();
        if($this->GetUserRole($User)){
            $db->Query("UPDATE :n :u WHERE :n=:d AND :n=:d",
                        'projects_users',array('role'=>$role),
                        'projectid',$this->ID,
                        'userid',$User->ID
                    );
        }else{
            $prop = array(
                "projectid"=>$this->ID,
                "userid"=>$User->ID,
                "role"=>$role
            );
            $db->Query("INSERT INTO :n :i",'projects_users',$prop);
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
        $db->Query("DELETE FROM :n WHERE :n=:d",'projects','id',$this->ID);
        $db->Query("DELETE FROM :n WHERE :n=:d",'projects_users','projectid',$this->ID);
        $this->Terms-> DeleteAllTerms();
        return true;
    }

    public function MakePublic($code){
        $db = new DB();
        $prop = array(
            'is_public'=>true,
            'public_link'=>$code
        );
        $db->Query("UPDATE :n :u WHERE :n=:d",'projects',$prop,'id',$this->ID);
    }

    public function MakePrivate(){
        $db = new DB();
        $prop = array(
            'is_public'=>false
        );
        $db->Query("UPDATE :n :u WHERE :n=:d",'projects',$prop,'id',$this->ID);
    }
}

class Terms{
    var $Project;
    var $TermsQueue;

    public function __construct($Project){
        $this->Project = $Project;
        $this->TermsQueue = array();
    }

    public function GetByName($name){
        $db = new DB();
        return $db->GetRow("SELECT * FROM :n WHERE :n=:s",'terms','name',$name);
    }

    public function GetList(){
        $db = new DB();
        return $db->GetArray("SELECT * FROM :n WHERE :n=:d",'terms','projectid',$this->Project->ID);
    }

    public function Find(){
        $db = new DB();

        $query = false;
        $start=0;
        $lines=false;

        $args = func_get_args();
        $num = func_num_args();
           
        switch($num){
            case 1:
                if(is_numeric($args[0])) 
                    $lines=$args[0];
                elseif(is_string($args[0]))     
                    $query=$args[0];
            break;

            case 2:
                if(is_numeric($args[0]) && is_numeric($args[1])){
                    $start = $args[0];
                    $lines = $args[1];
                }elseif(is_string($args[0]) && is_numeric($args[1])){
                    $query = $args[0];
                    $lines = $args[1];
                }
            break;

            case 3:
                if(is_string($args[0]) && is_numeric($args[1]) && is_numeric($args[2])){
                    $query = $args[0];
                    $start = $args[1];
                    $lines = $args[2];
                }
            break;
        }
        if(empty($query)) $query=false;

        $search = $db->Part();
        $limit = $db->Part();
        if($query) $search->Add(" AND :n LIKE :s",'name',"%$query%");
        if($lines) $limit->Add(" LIMIT :d,:d",$start,$lines);

        
        $terms = array();
        if(!$terms = $db->GetArray("SELECT :n FROM :n WHERE :n=:d:p:p",
                                    array('id','name'),'terms',
                                    'projectid',$this->Project->ID,
                                    $search,$limit)) return false;
        return $terms;
    }

    public function Num(){
        $db = new DB();
        return $db->GetCell("SELECT COUNT(1) FROM :n WHERE :n=:d",'terms','projectid',$this->Project->ID);
    }

    public function AddQueue($term){
        $this->TermsQueue[] = $term;
    }

    public function SaveQueue(){
        $db = new DB();
        $num = count($this->TermsQueue);
        if($num == 0) return false;
        
        if($list = $this->GetList()){
            foreach($list as $term){
                $key = array_search($term->name,$this->TermsQueue);
                if($key === false) continue;

                unset($this->TermsQueue[$key]);
                $num--;
            }
        }
        if($num == 0) return false;

        $props = array();
        foreach($this->TermsQueue as $name){
            $props[] = array(
                'projectid'=>$this->Project->ID,
                'name'=>$name
            );
        }
        $db->Query("INSERT INTO :n :i",'terms',$props);
    }

    public function AddTerm($name){
        if($this->GetByName($name)) return false;
        $db = new DB();
        $prop = array(
                        'projectid'=>$this->Project->ID,
                        'name'=>$name
                    );
        $db->Query("INSERT :n :i",'terms',$prop);
        return true;
    }

    public function SaveTerm($termid,$name){
        $db = new DB();
        $prop = array('name'=>$name);
        $db->Query("UPDATE :n :u WHERE :n=:d",'terms',$prop,'id',$termid);
    }

    public function DeleteTerm($termid){
        $db = new DB();
        $db->Query("DELETE FROM :n WHERE :n=:d",'terms','id',$termid);
    }

    public function DeleteAllTerms(){
        $db = new DB();
        $db->Query("DELETE FROM :n WHERE :n=:d",'terms','projectid',$this->Project->ID);
    }
}
?>