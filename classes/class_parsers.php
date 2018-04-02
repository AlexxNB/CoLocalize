<?php 
    class Parsers{
        public function __construct($lang='en'){

        }

        public function GetParser($parser){
            $path = __DIR__."/parsers/$parser/_parser.php";
            if(!file_exists($path)) return false;
            require($path);
            $Parser = new Parser();
            return $Parser;
        }
    }
?>