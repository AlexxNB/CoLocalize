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
		$db = new DB();
		if($db->SelectRow('users',"email='$email'")) return true;
		return false;
	}

  	public function Register($email,$password,$name){
		$db = new DB();
		$utils = new Utils();
        $user = array(
            'email'=>$email,
            'password'=>md5($password),
            'name'=>$name
        );
        return $db->Insert('users',$user);
	}
	  
  	public function Login($email,$password,$remember=true){
		$db = new DB();
		if(!$this->_isValid($email,$password)) return false;
		$user = $db->selectRow("users","email='$email'");

		$secure =  $this->_makeSecure($user['id'],$user['user'],$user['password']);

		$_SESSION['userid'] = $user['id'];
		$_SESSION['securehash'] = $secure;


		if($remember)
		{
		setcookie("userid", $admin['id'], time() + 3600*24*365,'/');
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
		if($user) $user = $this->_userDecodeArrays($user);
		return $utils->SetGlobal('auth_user',$user);
	}
   
	public function GetUserById($userid){
		$db = new DB();
		$utils = new Utils();
		if($utils->IsGlobal('auth_user_'.$userid)) return $utils->GetGlobal('auth_user_'.$userid);
		$user =  $db->SelectRow('users','id='.$userid);
		if($user) $user = $this->_userDecodeArrays($user);
		return $utils->SetGlobal('auth_user_'.$userid,$user);
	}

	public function HasPriv($userid,$priv){
		if(!$user = $this->GetUserById($userid)) return false;
		if(in_array($priv,$user['privs'])) return true;
		elseif(in_array('god',$user['privs'])) return true;
		else return false;
	}

	public function AddPriv($userid,$priv){
		if(!$user = $this->GetUserById($userid)) return false;
		if(in_array($priv,$user['privs'])) return false;
		$user['privs'][]=$priv;
		$db = new DB();
		$db->Update('users', array('privs'=>json_encode($user['privs'])), "id=$userid");
	}

	public function RemovePriv($userid,$priv){
		if(!$user = $this->GetUserById($userid)) return false;
		if(!in_array($priv,$user['privs'])) return false;
		unset($user['privs'][array_search($priv,$user['privs'])]);
		$db = new DB();
		$db->Update('users', array('privs'=>json_encode($user['privs'])), "id=$userid");
  	}
  
  	private function _userDecodeArrays($user){
		if($privs = json_decode($user['privs'],true)) 
			$user['privs']=$privs;
		else
			$user['privs']=array();
		return $user;
  	}
  
  	private function _isValidSession(){
		$db = new DB();
		$user = $db->selectRow('users','id='.$_SESSION['userid']);
		$secure =  $this->_makeSecure($user['id'],$user['user'],$user['password']);
		if($_SESSION['securehash'] != $secure) return false;
		return true;
	}

	private function _isValid($email,$password){
		$db = new DB();
    	if(!$user = $db->selectRow("users","email='$email'")) return false;
		if(md5($password) != $user['password']) return false;
		return true;
	}

	private function _makeSecure($userid,$user,$password){
		$salt = 'salt'; //Change "salt", if want a little bit more security =)
		return md5($salt.$userid.$user.$password.$salt);  
	}
}
?>
