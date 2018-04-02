<?php
class Parser{
    // class Parser should contents three functions ImportTerms, ImportTranslation, ExportTranslation.

    // Import terms from the file content.
    public function ImportTerms($content){
        $Terms = array();
        if(!$strings = $this->ImportTranslation($content)) return false;
        foreach($strings as $term=>$value){
            $Terms[]=$term;
        }
        return $Terms;
    }

    // Import term->value strings from the file content.
    public function ImportTranslation($content){
        if(!$array = json_decode($content,true)) return false;
        $strings = $this->_arrayToStrings($array);
        return $strings;
    }


    public function _arrayToStrings($array,$parent=''){
        if($parent != '') $parent = $parent.':';
        $Struct = array();
        foreach($array as $key=>$value){
            if(is_array($value)){
                $Struct =  array_merge($Struct,$this->_makeStructure($array[$key],$parent.$key));
            }else{
                $Struct[$parent.$key]=$value;
            }
        }
        return $Struct;
    }
}
?>