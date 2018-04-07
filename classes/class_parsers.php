<?php 
require_once("class_language.php");

    class Parsers{
        public function __construct(){

        }

        public function GetParser($parser){
            $path = __DIR__."/../parsers/$parser/_parser.php";
            if(!file_exists($path)) return false;
            require($path);
            $parserClass = "Parser_$parser";
            if(!class_exists($parserClass)) return false;
            $Parser = new $parserClass($parser);
            if(!method_exists($Parser,'ImportTerms') || !method_exists($Parser,'ImportTranslation') || !method_exists($Parser,'ExportTranslation')) return false;
            return $Parser;
        }

        public function GetParsersList(){
            $skip = array('.', '..');
            $files = scandir(__DIR__ . "/../parsers");
            $list = array();
            foreach($files as $parser) {
                if(in_array($parser, $skip)) continue;
                if(!$Parser = $this->GetParser($parser)) continue;
                $list[$parser] = $Parser;
            }
            return $list;
        }
    }

    class Parser{
        var $ID;
        var $Title;
        var $Filetype;
        var $Website;
        var $_path;

        public function __construct($parser){
            $this->ID = $parser;
            $this->_path = __DIR__ . "/../parsers/$this->ID/";
            $this->_fetchInfo();
        }

        private function _fetchInfo(){
            $lang = new Language($this->_path.'/locales/');
            $L = $lang->GetLangVars();
            $this->Title = $L['title'];
            if(!file_exists($this->_path.'_info.php')) return false;
            require($this->_path.'_info.php');
            $this->Filetype = $filetype;
            $this->Website = $website;
        }
    }
?>