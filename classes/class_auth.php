<?php
session_start(); 
require_once("class_litedb.php");
require_once("class_utils.php");
//require_once("class_mail.php");
class Auth
{
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
    $db = new LiteDB();
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
    $db = new LiteDB();
    $user = $db->selectRow('users','id='.$_SESSION['userid']);
    $db->close();
    $secure =  $this->makeSecure($user['id'],$user['user'],$user['password']);
    if($_SESSION['securehash'] != $secure) return false;
    return true;
  }

  private function _isValid($user,$password){
    $db = new LiteDB();
    if(!$admin = $db->selectRow("users","user='$user'")) return false;
    if(md5($password) != $admin['password']) return false;

    return true;
  }

  private function makeSecure($userid,$user,$password)
  {
    return md5('alexxnb'.$userid.$user.$password.'4kj5cigc3x7jdzIv7653tv');
  }

  public function IsUser($user){
     $db = new LiteDB();
     $user = $db->selectRow("users","user='$user'");
       if($user)
         return true;
       else
         return false;
  }

  private function _userDecodeArrays($user){
      if($privs = json_decode($user['privs'],true)) 
        $user['privs']=$privs;
      else
        $user['privs']=array();
      return $user;
  }

  public function GetUser(){
    $db = new LiteDB();
    $utils = new Utils();

    if($utils->isGlobal('auth_user')) return $utils->getGlobal('auth_user');

    if(!$this->isAuthed()) return $utils->setGlobal('auth_user',false);
    $user = $db->selectRow('users','id='.$_SESSION['userid']);
    if($user) $user = $this->_userDecodeArrays($user);
    return $utils->setGlobal('auth_user',$user);
  }

  public function GetUserById($userid){
    $db = new LiteDB();
    $utils = new Utils();
    if($utils->isGlobal('auth_user_'.$userid)) return $utils->getGlobal('auth_user_'.$userid);
    $user =  $db->selectRow('users','id='.$userid);
    if($user) $user = $this->_userDecodeArrays($user);
    return $utils->setGlobal('auth_user_'.$userid,$user);
  }

  public function HasPriv($userid,$priv){
    if(!$user = $this->GetUserById($userid)) return false;
    if(in_array($priv,$user['privs'])) return true;
    elseif(in_array('god',$user['privs'])) return true;
    else return false;
  }

  public function GetPrivArray($userid,$priv){
    $privs = array();
    if(!$user = $this->GetUserById($userid)) return false;
    foreach($user['privs'] as $line){
      if(preg_match('|^'.$priv.'_(.+)$|',$line,$m)) $privs[] = $m[1];
    }
    if(count($privs)==0) return false;
    return $privs;
  }

  public function AddPriv($userid,$priv){
    if(!$user = $this->GetUserById($userid)) return false;
    if(in_array($priv,$user['privs'])) return false;
    $user['privs'][]=$priv;
    $db = new LiteDB();
    $db->Update('users', array('privs'=>json_encode($user['privs'])), "id=$userid");
  }

  public function RemovePriv($userid,$priv){
    if(!$user = $this->GetUserById($userid)) return false;
    if(!in_array($priv,$user['privs'])) return false;
    unset($user['privs'][array_search($priv,$user['privs'])]);
    $db = new LiteDB();
    $db->Update('users', array('privs'=>json_encode($user['privs'])), "id=$userid");
  }

   /*function isAuthed($type='')
   {
      $utils = new Utils();
      $db = new MyDB();
      
      if($utils->isGlobal('auth_isauth_'.$type)) return $utils->getGlobal('auth_isauth_'.$type);
      
      if(isset($_COOKIE['userid'])) $_SESSION['userid']=$_COOKIE['userid'];
      if(isset($_COOKIE['securehash'])) $_SESSION['securehash']=$_COOKIE['securehash'];


      if(!isset($_SESSION['userid'])) return $utils->setGlobal('auth_isauth_'.$type,false);
      if(!isset($_SESSION['securehash'])) return $utils->setGlobal('auth_isauth_'.$type,false);

      if(!$this->isValidSession()) return $utils->setGlobal('auth_isauth_'.$type,false);
      $user = $this->getUserById($_SESSION['userid']);
      if($user) $db->update("users",array("last_visit"=>time()),"id='$user[id]'");

      if( $type=='')  return $utils->setGlobal('auth_isauth_'.$type,true);
      if($type == $user['type']) return $utils->setGlobal('auth_isauth_'.$type,true);
  
      return $utils->setGlobal('auth_isauth_'.$type,false);
   }


   function login($user,$password,$remember=true)
   {
        $db = new MyDB();

        if(!$this->IsValid($user,$password)) return false;
        $admin = $db->selectRow("users","user='$user'");

        $secure =  $this->makeSecure($admin['id'],$admin['user'],$admin['password'],$admin['type']);

        $_SESSION['userid'] = $admin['id'];
        $_SESSION['securehash'] = $secure;


        if($remember)
        {
            setcookie("userid", $admin['id'], time() + 3600*24*365,'/');
            setcookie("securehash", $secure, time() + 3600*24*365,'/');
        }

        $db->update("users",array("lastip"=>$admin['ip'],"ip"=>$_SERVER["REMOTE_ADDR"]),"user='$user'");
        return true;
   }

   function makeSecure($userid,$user,$password,$type)
   {
      return md5('alexxnb'.$userid.$user.$type.$password.'pua2345gs8ddlk5vaosi');
   }

   function logout()
   {
      unset($_SESSION['userid']);
      unset($_SESSION['securehash']);

      setcookie("userid", "", time() - 3600,'/');
      setcookie("securehash", "", time() - 3600,'/');
   }

   function isValidSession()
   {
      $db = new MyDB();
      $user = $db->selectRow('users','id='.$_SESSION['userid']);

      $secure =  $this->makeSecure($user['id'],$user['user'],$user['password'],$user['type']);


      if($_SESSION['securehash'] != $secure) return false;
      return true;
   }

   function isValid($user,$password,$type=false,$md5=false)
   {
     $db = new MyDB();
     if(!$admin = $db->selectRow("users","user='$user'")) return false;

     if(!$md5)
       {if($password != $admin['password']) return false;}
     else
       if($password != md5($admin['password'])) return false;

     if($type)
     if($type != $admin['type']) return false;

     return true;
   }

   function getLastIP()
   {
     $db = new MyDB();
     if(!$this->Authed()) return false;
     $db->select("users","id='".$_SESSION['userid']."'");
     $admin = $db->fetch();
     return $admin['lastip'];
   }

   function authRequire()
   {
     if(!$this->isAuthed())
     {
        $_SESSION['reqpage']=$_SERVER["REQUEST_URI"];
        header("LOCATION: /login.php");
        exit;
     }
   }

   function isUser($user)
   {
      $db = new MyDB();
      $db->select("users","user='$user'");
        if($db->numrows() == 0)
          return false;
        else
          return true;
   }

   function isEmail($email)
   {
      $db = new MyDB();
      return $db->selectCell("users",'id',"email='$email'");
   }

   function addUser($user,$password,$email,$type)
   {
     $db = new MyDB();
     return $db->Insert('users',array(
                                         'user'=>$user,
                                         'password'=>$password,
                                         'type'=>$type,
                                         'email'=>$email,
                                         'ip'=>$_SERVER["REMOTE_ADDR"],
                                         'lastip'=>$_SERVER["REMOTE_ADDR"]
                                       ));
   }

   function savePassword($userid,$password)
   {
      $db = new MyDB();
      $db->update("users",array("password"=>$password),"id=$userid");
   }

   function saveEmail($userid,$email)
   {
      $db = new MyDB();
      $db->update("users",array("email"=>$email),"id=$userid");
   }

   function sendPassword($email)
   {
       if(!$this->isEmail($email)) return false;
         $db = new MyDB();
         $mail = new eMail();
         $user = $db->selectRow('users',"email='$email'");

         $login = htmlspecialchars_decode($user['user']);
         $password = htmlspecialchars_decode($user['password']);


         $code =  $this->getCode($login);


         $body = "Здравствуйте, $login!

Вы или кто-то другой решили восстановить пароль для входа на сервис Post-Tracker.ru:

Для восстановления пароля перейдите по указанной ссылке:
http://post-tracker.ru/chpass.php?l=".urlencode($login)."&c=$code

С уважением, админстрация сервиса Post-Tracker.ru
";

   return $mail->send($email,'Восстановление пароля Post-Tracker.ru',$body);

   }

   function getUser()
   {
      $db = new MyDB();
      $utils = new Utils();

      if($utils->isGlobal('auth_user')) return $utils->getGlobal('auth_user');

      if(!$this->isAuthed()) return $utils->setGlobal('auth_user',false);
      $user = $db->selectRow('users','id='.$_SESSION['userid']);
      return $utils->setGlobal('auth_user',$user);
   }

   function getUserByLogin($login)
   {
      $db = new MyDB();
      return $db->selectRow('users',"user='$login'");
   }

   function getUserById($id)
   {
      $db = new MyDB();
      return $db->selectRow('users',"id='$id'");
   }

   function getUserByEmail($email)
   {
      $db = new MyDB();
      return $db->selectRow('users',"email='$email'");
   }

   function getCode($user,$len=6)
   {
      $salt = 'anb03121985';
      return substr(md5($salt.$user.$salt),2,$len);
   }

   function setDefaultDestination($userid,$dest)
   {
      $db = new MyDB();
      $dest = str_replace('ems_','',$dest);
      $db->update("users",array("defdest"=>$dest),"id=$userid");
   }

   function getDefaultDestination($userid)
   {
      $db = new MyDB();
      $dest = $db->selectCell('users','defdest',"id='$userid'");
      if(empty($dest)) $dest=false;

      return $dest;
   }
   
   function setTelegramID($userid,$telegramid)
   {
	  $db = new MyDB();
      $db->update("users",array("telegramid"=>$telegramid),"id=$userid"); 
   }
   
   function getTelegramID($userid)
   {
	   $db = new MyDB();
	   $tid = $db->selectCell('users','telegramid',"id='$userid'");
       if($tid == 0) $tid=false;

       return $tid;
   }
   
   function setVKID($userid,$vkid)
   {
	  $db = new MyDB();
      $db->update("users",array("vkid"=>$vkid),"id=$userid"); 
   }
   
   function getVKID($userid)
   {
	   $db = new MyDB();
	   $tid = $db->selectCell('users','vkid',"id='$userid'");
       if($tid == 0) $tid=false;

       return $tid;
   }*/
}
?>
