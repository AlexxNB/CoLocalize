<?php
class Utils{

  public function setGlobal($name,$value){
    $GLOBALS['myglobals'][$name] = $value;
    return $value;
  }

  public function isGlobal($name){
    return isset($GLOBALS['myglobals'][$name]);
  }

  public function getGlobal($name){
    if(!$this->isGlobal($name)) return false;
    return $GLOBALS['myglobals'][$name];
  }

  public function unsetGlobal($name){
      unset($GLOBALS['myglobals'][$name]);
  }
}
?>