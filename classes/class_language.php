<?php
require_once("class_utils.php");
class Language {
    var $_defLang = 'en';
    var $_delimeter = ':';
    var $_path;

    public function __construct($path=false){
        if($path) 
            $this->_path = $path;
        else
            $this->_path = __DIR__."/../res/locales/";
    }

    public function GetLanguage(){
        $utils = new Utils();
        if($utils->isGlobal('lang')) return $utils->getGlobal('lang');
        return $this->_defLang;
    }

    public function SetLanguage($lang){
        $utils = new Utils();
        if(!$this->_isLang($lang)) $lang=$this->_defLang;
        return $utils->SetGlobal('lang',$lang);
    }

    public function GetLangVars($lang=false,$baselang=false){
        if(!$lang) $lang=$this->GetLanguage();
        if(!$baselang) $baselang = $this->_defLang;
        $BaseStrings = $this->_getLangFile($baselang);
        if(!$BaseStrings) return array();

        if($lang == $baselang) 
            $Strings=$BaseStrings;
        else
            $Strings = $this->_getLangFile($baselang);

        if(!$Strings) $Strings=$BaseStrings;
        
        return $this->_makeLangStructure($String,$BaseStrings,'');
    }

    public function _makeLangStructure($strings,$baseStrings,$parent){
        if($parent != '') $parent = $parent.$this->_delimeter;
        $Struct = array();
        foreach($baseStrings as $key=>$value){
            if(is_array($value)){
               $Struct =  array_merge($Struct,$this->_makeLangStructure($strings[$key],$baseStrings[$key],$parent.$key));
            }else{
                if(isset($strings[$key]))
                    $Struct[$parent.$key]=$strings[$key];
                else
                    $Struct[$parent.$key]=$value;
            }
        }
        return $Struct;
    }

    private function _getPath($lang){
        return $this->_path.$lang.".json";
    }

    private function _getLangFile($lang){
        if(!$this->_isLang($lang)) return false;
        $path = $this->_getPath($lang);
        $file = file_get_contents($path);
        return json_decode($file,true);
    }

    private function _isLang($lang){
        if(file_exists($this->_getPath($lang))) return true;
        return false;
    }

}
?>