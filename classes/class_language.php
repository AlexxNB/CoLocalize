<?php
class Language {
    var $_delimeter = ':';

    public function __construct(){
    }

    public function GetLangVars($lang,$baselang='en'){
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

    private function _getLangFile($lang){
        $path = __DIR__."/../res/locales/$lang.json";

        if(!file_exists($path)) return false;

        $file = file_get_contents($path);
        return json_decode($file,true);
    }

}
?>