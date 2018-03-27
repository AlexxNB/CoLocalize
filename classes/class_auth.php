<?php
session_start(); 
require_once("class_db.php");
require_once("class_utils.php");
class Auth
{
    public function IsEmail($email){
        $db = new DB();
        if($db->SelectRow('users',"email='$email'")) return true;
        return false;
    }

    public function Register($email,$password,$name){
        $db = new DB();
        $user = array(
            'email'=>$email,
            'password'=>md5($password),
            'name'=>$name
        );
        return $db->Insert('users',$user);
    }
  /*
  public function isAuthed(){
    $utils = new Utils();
    if($utils->isGlobal('auth_isauth')) return $utils->getGlobal('auth_isauth');

    if(isset($_COOKIE['userid'])) $_SESSION['userid']=$_COOKIE['userid'];
    if(isset($_COOKIE['securehash'])) $_SESSION['securehash']=$_COOKIE['securehash'];
    if(!isset($_SESSION['userid'])) return $utils->setGlobal('auth_isauth',false);
    if(!isset($_SESSION['securehash'])) return $utils->setGlobal('auth_isauth',false);

    if(!$this->_isValidSession()) return $utils->setGlobal('auth_isauth',false);

    return $utils->setGlobal('auth_isauth',true);
  }

  public function Login($user,$password,$remember=true){
    $db = new DB();
    if(!$this->_isValid($user,$password)) return false;
    $admin = $db->selectRow("users","user='$user'");

    $secure =  $this->makeSecure($admin['id'],$admin['user'],$admin['password']);

    $_SESSION['userid'] = $admin['id'];
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

  private function _isValidSession(){
    $db = new DB();
    $user = $db->selectRow('users','id='.$_SESSION['userid']);
    $db->close();
    $secure =  $this->makeSecure($user['id'],$user['user'],$user['password']);
    if($_SESSION['securehash'] != $secure) return false;
    return true;
  }

  private function _isValid($user,$password){
    $db = new DB();
    if(!$admin = $db->selectRow("users","user='$user'")) return false;
    if(md5($password) != $admin['password']) return false;

    return true;
  }

  private function makeSecure($userid,$user,$password)
  {
    return md5('alexxnb'.$userid.$user.$password.'4kj5cigc3x7jdzIv7653tv');
  }

  public function IsUser($user){
     $db = new DB();
     $user = $db->selectRow("users","user='$user'");
       if($user)
         return true;
       else
         return false;
  }

  public function GetUser(){
    $db = new DB();
    $utils = new Utils();

    if($utils->isGlobal('auth_user')) return $utils->getGlobal('auth_user');

    if(!$this->isAuthed()) return $utils->setGlobal('auth_user',false);
    $user = $db->selectRow('users','id='.$_SESSION['userid']);
    if($user) $user = $this->_userDecodeArrays($user);
    return $utils->setGlobal('auth_user',$user);
  }
   */
  private function _userDecodeArrays($user){
      if($privs = json_decode($user['privs'],true)) 
        $user['privs']=$privs;
      else
        $user['privs']=array();
      return $user;
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
 
}
?>
