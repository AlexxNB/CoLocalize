<?php
class Utils{

  public function SetGlobal($name,$value){
    $GLOBALS['myglobals'][$name] = $value;
    return $value;
  }

  public function IsGlobal($name){
    return isset($GLOBALS['myglobals'][$name]);
  }

  public function GetGlobal($name){
    if(!$this->isGlobal($name)) return false;
    return $GLOBALS['myglobals'][$name];
  }

  public function UnsetGlobal($name){
      unset($GLOBALS['myglobals'][$name]);
  }
}
?>