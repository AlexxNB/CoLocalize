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

    public function GetProjectByLangId($lid){
        $db = new DB();
        if(!$pid = $db->GetCell("SELECT :n FROM :n WHERE :n=:d",'projectid','languages','id',$lid)) return false;
        return $this->GetProject($pid);
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
    public $Langs;

    public function __construct($projectid=false){
        if($projectid) {
            $this->ID = $projectid;
            $this->Terms = new Terms($this);
            $this->Langs = new Languages($this);
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

    public function CheckUserRole(){
        $args = func_get_args();
        if(count($args) < 2) return false;
        $User = array_shift($args);
        if(!$User instanceof User) return false;
        if(!$cRole = $this->GetUserRole($User)) return false;
        foreach($args as $role){
            if($role == $cRole) return true;
        }
        return false;
    }

    public function CanUserDo($User,$action,$data=false){
        switch($action){
            case 'view_project':
            case 'add_language':
                if($this->CheckUserRole($User,'admin','contributor')) return true;
            break;
            
            case 'create_project':
            case 'delete_project':
            case 'edit_project':
            case 'import_terms':
            case 'edit_terms':
                if($this->CheckUserRole($User,'admin')) return true;
            break;

            case 'delete_language':
            case 'translate':
                if($this->CheckUserRole($User,'admin')) return true;
                if($this->Langs->IsCreator($data,$User)) return true;
            break;

            default: 
                return false;
        }
        return false;
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

    public function DeleteTerms($termids){
        if(!is_array($termids)) return false;
        $db = new DB();
        $db->Query("DELETE FROM :n WHERE :n IN :l",'terms','id',$termids);
    }

    public function DeleteAllTerms(){
        $db = new DB();
        $db->Query("DELETE FROM :n WHERE :n=:d",'terms','projectid',$this->Project->ID);
    }
}

class Languages{
    var $Project;

    public function __construct($Project){
        $this->Project = $Project;
    }

    public function GetUnusedList(){
        $list = $this->Info();
        $used = $this->GetList();
        foreach($used as $code=>$Lang){
            if(array_key_exists($code,$list)) unset($list[$code]);
        }
        return $list;
    }

    public function GetList(){

        $db = new DB();
        $list = array();
        $listDB = $db->GetArray("SELECT * FROM :n WHERE :n=:d",'languages','projectid',$this->Project->ID);
        if($listDB) {
            foreach($listDB as $line){
                $list[$line->code] = $this->_makeLangObj($line);
            }            
        }
        return $list;
    }

    public function Get($lang){
        $db = new DB();
        $Part = $db->Part();
        if(preg_match('|^\d+$|',$lang))
            $Part->Add(":n=:d",'id',$lang);
        elseif(preg_match('|^[a-zA-Z]+$|',$lang))
            $Part->Add(":n=:d AND :n=:s",'projectid',$this->Project->ID,'code',$lang);
        else
            return false;

        if(!$langDB = $db->GetRow("SELECT * FROM :n WHERE :p",'languages',$Part)) return false;
        return $this->_makeLangObj($langDB);
    }

    public function IsInList($code){
        $db = new DB();
        if($db->GetRow("SELECT :n FROM :n WHERE :n=:d AND :n=:s",
                        'id','languages',
                        'projectid',$this->Project->ID,
                        'code',$code
                        )) return true;
        return false;
    }
    
    public function Info(){
        $args = func_get_args();
        if(!$listAr = $this->_getListArray()) return false;
        if(count($args)==0){
            $list = array();
            foreach($listAr as $code=>$data){
                $list[$code] = $this->_makeListLangObj($data);
            }
            return $list;
        }else{
            $list = array();
            foreach($args as $code){
                if(!isset($listAr[$code]))  continue;
                $list[$code] = $this->_makeListLangObj($listAr[$code]);
            }
            $num = count($list);
            if($num == 0) return false;
            if($num == 1) return array_shift($list);
            return $list;
        }
    }

    public function Add($code,$Lang,$User){
        $db = new DB();
        $prop = array(
            'projectid'=>$this->Project->ID,
            'code'=>$code,
            'name'=>$Lang->name,
            'native'=>$Lang->native,
            'orign'=>'en',
            'creator'=>$User->ID
        );
        $db->Query("INSERT INTO :n :i",'languages',$prop);
        $prop["id"] = $db->LastID();
        return $this->_makeLangObj($prop);
    }

    public function Delete($lid){
        $db = new DB();
        $db->Query("DELETE FROM :n WHERE :n=:d",'languages','id',$lid);
    }

    public function IsCreator($lid,$User){
        $db = new DB();
        if(!$uid = $db->GetCell("SELECT :n FROM :n WHERE :n=:d",'creator','languages','id',$lid)) return false;

        if($User->ID == $uid) return true;
        return false;
    }

    private function _getListArray(){
        if(!$json = file_get_contents('res/languages/list.json',true)) return false;
        if(!$ar = json_decode($json,true)) return false;
        return $ar;
    }

    private function _makeListLangObj($dataDB){
        if(is_array($dataDB)) $dataDB = json_decode(json_encode($dataDB), FALSE);
        $Lang = new stdClass();
        $Lang->name = $dataDB->name;
        $Lang->native = $dataDB->nativeName;

        return $Lang;
    }

    private function _makeLangObj($dataDB){
        if(is_array($dataDB)) $dataDB = json_decode(json_encode($dataDB), FALSE);
        $Lang = new Lang($this->Project);
        $Lang->ID = $dataDB->id;
        $Lang->Code = $dataDB->code;
        $Lang->Name = $dataDB->name;
        $Lang->Native = $dataDB->native;
        $Lang->Creator = $dataDB->creator;
        $Lang->Orign = $dataDB->orign;

        return $Lang;
    }
}

class Lang{
    public $ID;
    public $Code;
    public $Name;
    public $Native;
    public $Orign;
    public $Creator;
    public $Project;

    public function __construct($Project){
        $this->Project = $Project;
    }

    public function SetOrign($code){
        $db = new DB();
        if(!$Project->Langs->IsInList($code)) return false;

        $prop = array('orign'=>$code);
        $db->Query("UPDATE :n :u WHERE :n=:d",'languages',$prop,'id',$this->ID);
    }
}
?>