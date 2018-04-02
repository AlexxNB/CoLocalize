<?php
session_start(); 
require_once("class_db.php");
require_once("class_utils.php");
class Auth
{
	public function IsAuthed(){
		$utils = new Utils();
		if($utils->IsGlobal('auth_isauth')) return $utils->GetGlobal('auth_isauth');

		if(isset($_COOKIE['userid'])) $_SESSION['userid']=$_COOKIE['userid'];
		if(isset($_COOKIE['securehash'])) $_SESSION['securehash']=$_COOKIE['securehash'];
		if(!isset($_SESSION['userid'])) return $utils->SetGlobal('auth_isauth',false);
		if(!isset($_SESSION['securehash'])) return $utils->SetGlobal('auth_isauth',false);

		if(!$this->_isValidSession()) return $utils->SetGlobal('auth_isauth',false);

		return $utils->SetGlobal('auth_isauth',true);
	}

	public function IsEmail($email){
		if($this->GetUserByEmail($email)) return true;
		return false;
	}

  	public function Register($email,$password,$name){
		$db = new DB();
		$utils = new Utils();
        $user = array(
            'email'=>$email,
            'password'=>md5($password),
			'name'=>$name,
			'privs'=>'[]'
        );
		if(!$uid = $db->Insert('users',$user)) return false;
		$user['id']=$uid;
		return $this->_makeUserObj($user);
	}
	  
  	public function Login($email,$password,$remember=true){
		if(!$this->_isValid($email,$password)) return false;
		if(!$User = $this->GetUserByEmail($email)) return false;

		$secure =  $this->_makeSecure($User->ID,$User->Email,$User->PassHash);

		$_SESSION['userid'] = $User->ID;
		$_SESSION['securehash'] = $secure;


		if($remember){
			setcookie("userid", $User->ID, time() + 3600*24*365,'/');
			setcookie("securehash", $secure, time() + 3600*24*365,'/');
		}
		return true;
  	}

	public function Logout(){
		unset($_SESSION['userid']);
		unset($_SESSION['securehash']);

		setcookie("userid", "", time() - 3600,'/');
		setcookie("securehash", "", time() - 3600,'/');
	}
  

	public function GetUser(){
		$db = new DB();
		$utils = new Utils();

		if($utils->IsGlobal('auth_user')) return $utils->GetGlobal('auth_user');

		if(!$this->IsAuthed()) return $utils->SetGlobal('auth_user',false);
		$user = $db->SelectRow('users','id='.$_SESSION['userid']);
		if($user) $user = $this->_makeUserObj($user);
		return $utils->SetGlobal('auth_user',$user);
	}
   
	public function GetUserById($userid){
		$db = new DB();
		$utils = new Utils();
		if($utils->IsGlobal('auth_user_'.$userid)) return $utils->GetGlobal('auth_user_'.$userid);
		$user =  $db->SelectRow('users','id='.$userid);
		if($user) $user = $this->_makeUserObj($user);
		return $utils->SetGlobal('auth_user_'.$userid,$user);
	}

	public function GetUserByEmail($email){
		$db = new DB();
		if(!$user = $db->selectRow("users","email='$email'")) return false;
		return $user = $this->_makeUserObj($user);
	}
  
	private function _makeUserObj($data){
		$User = new User();
		$User->ID = $data['id'];
		$User->Email = $data['email'];
		$User->PassHash = $data['password'];
		$User->Name = $data['name'];
		if($privs = json_decode($data['privs'],true)) 
			$User->Privs=$privs;
		else
			$User->Privs=array();
		
		return $User;
	}
  
  	private function _isValidSession(){
		if(!$User = $this->GetUserById($_SESSION['userid'])) return false;
		$secure =  $this->_makeSecure($User->ID,$User->Email,$User->PassHash);
		if($_SESSION['securehash'] != $secure) return false;
		return true;
	}

	private function _isValid($email,$password){
		if(!$User = $this->GetUserByEmail($email)) return false;
		if(md5($password) != $User->PassHash) return false;
		return true;
	}

	private function _makeSecure($userid,$email,$password){
		$salt = 'salt'; //Change "salt", if want a little bit more security =)
		return md5($salt.$userid.$email.$password.$salt);  
	}
}

class User{
	public $ID;
	public $Email;
	public $PassHash;
	public $Name;
	public $Privs;

	public function HasPriv($priv){
		if(in_array($priv,$this->Privs)) return true;
		elseif(in_array('god',$this->Privs)) return true;
		else return false;
	}

	public function AddPriv($priv){
		if(in_array($priv,$this->Privs)) return false;
		$this->Privs[]=$priv;
		$db = new DB();
		$db->Update('users', array('privs'=>json_encode($this->Privs)), "id=$this->ID");
	}

	public function RemovePriv($priv){
		if(!in_array($priv,$this->Privs)) return false;
		unset($this->Privs[array_search($priv,$this->Privs)]);
		$db = new DB();
		$db->Update('users', array('privs'=>json_encode($this->Privs)), "id=$this->ID");
  	}
}
?>
