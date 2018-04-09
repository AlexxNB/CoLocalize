<?php

class DB{
    const DEBUG = true;

    private $_host;
    private $_user;
    private $_password;
    private $_database;
    private $_port;
    private $_conn;

    public function __construct(){ 
        $this->_loadConfig();
        $pdo_mysql = 'mysql:host='. $this->_host .';dbname='. $this->_database .';port='. $this->_port;

        if(!isset($GLOBALS['DBConnected']))
        {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ];

            try {
                $GLOBALS['DBConnected'] = new PDO($pdo_mysql, $this->_user, $this->_password, $options);
            } catch (PDOException $e) {
                $this->_error($e->getMessage());
            }
        }
        $this->_conn = $GLOBALS['DBConnected'];
    }

    public function Query(){
        return $this->_query(func_get_args()); 
    }

    public function GetCell(){
        $res = $this->_query(func_get_args());
        $row = $this->_fetch($res,false);
        $this->_free($res);
        if(!$row) return false;
        return $row[0];        
    }

    public function GetRow(){
        $res = $this->_query(func_get_args());
        $row = $this->_fetch($res);
        $this->_free($res);
        return $row;
    }

    public function GetArray(){
        $res = $this->_query(func_get_args());

        if($this->_isEmpty($res)){
            $this->_free($res);
            return false;
        }else{
            $array = array();
            while($row = $this->_fetch($res)){
                $array[] = $row;
            }
            $this->_free($res);
            return $array;
        }
    }

    public function Part(){
        $args = func_get_args();
        if(count($args) == 0) $this->_error("Require at least one argument");
        $sample = array_shift($args);
        $query = $this->_prepare($sample,$args);
        return new DBPart($query);
    }

    private function _loadConfig(){
        require(dirname(__FILE__)."/../_config.php");
        $this->_host = $server;
        $this->_user = $user;
        $this->_password = $password;
        $this->_database = $database;
        $this->_port = $port;
    }

    private function _error($error){
        if(self::DEBUG){
            echo "DB Error in ".debug_backtrace()[1]['function'].": ";
            echo "\r\n";
            echo $error;
        }else{
            header("HTTP/1.0 500 Internal Server Error");
        }
        exit();
    }

    private function _query($args){
        if(count($args) == 0) $this->_error("Require at least one argument");
        $sample = array_shift($args);
        $query = $this->_prepare($sample,$args);
        return $this->_execute($query);
    }

    private function _execute($query){
        $result = NULL;

        try {
            $result = $this->_conn->query($query);
        } catch (PDOException $e) {
            $this->_error($e->getMessage()."\r\n".$query);
        }

        return $result;
    }

    private function _fetch($res,$object=true){
        if($object)
            return $res->fetch(PDO::FETCH_OBJ);
        else
            return $res->fetch(PDO::FETCH_BOTH);
    }

    private function _numRows($res){
        return $res->rowCount();
    }

    private function _isEmpty($res){
        if($this->_numRows($res) == 0) return true;
        else return false;
    }

    private function _lastID(){
        return $this->_conn->lastInsertId();
    }

    private function _free($res){
        $res->closeCursor();
    }


    private function _prepare($sample,$args){
        $rpNum = 0;
        $phNum = count($args);
        // :n - names, :d - digits, :s - string, :b - boolean, :i - array set for insert, :u - array set for update, :p - part of statement
        $prepared = preg_replace_callback('|(\:[bundisp])|',function($match) use(&$args,&$rpNum) { 
            $dirtyVal = array_shift($args);
            $clearVal = $this->_getClearValue($match[1],$dirtyVal);
            if(!$clearVal) return false;
            $rpNum++;
            return $clearVal;
        },$sample);

        if($rpNum != $phNum) $this->_error("Number of args is not equal number of placeholders in [$sample]");
        return $prepared;
    }

    private function _getClearValue($placeholder,$value){
        switch($placeholder){
            case ':n':
                return $this->_clearName($value);
            break;

            case ':d':
                return $this->_clearDigit($value);
            break;
            
            case ':s':
                return $this->_clearString($value);
            break;

            case ':b':
                return $this->_clearBool($value);
            break;

            case ':i':
                return $this->_makeInsert($value);
            break;

            case ':u':
                return $this->_makeUpdate($value);
            break;

            case ':p':
                return $this->_makePart($value);
            break;

            default:
                $this->_error("Unknown placeholder <$placeholder>");
            break;
        }
    }

    private function _clearName($value){
        if(!is_array($value)){
            if(empty($value)) $this->_error("Empty value for <:n> placeholder");
            $value = array($value);
        }
        $names = array();
        foreach($value as $v){
            $v = str_replace("`","``",$v);
            $names[] = "`$v`";
        }

        return join(',',$names);
    }

    private function _clearDigit($value){
		if ($value === NULL) return 'NULL';
		if(!is_numeric($value)) $this->_error("Got non numeric value for <:d> placeholder");
		if (is_float($value)) $value = number_format($value, 0, '.', '');
		return $value;
    }
    
    private function _clearString($value){
        if ($value === NULL) return 'NULL';
		return $this->_conn->quote($value);
    }

    private function _clearBool($value){
        if ($value === NULL) return 'NULL';
        if(!is_bool($value)) $this->_error("Got non boolean value for <:b> placeholder");
        if ($value === true) return 1; 
        return 'NULL';
    }

    private function _makeInsert($value){
        if(!is_array($value)) $this->_error("Got non array value for <:i> placeholder");
        if(!isset($value[0])) $value = array($value);
        $left = array();
        $right = array();
        $i=0;
        foreach($value as $set){
            $part = array();
            foreach($set as $k=>$v){
                if($i==0) $left[] = $this->_clearName($k);

                if(is_numeric($v))
                    $part[] = $this->_clearDigit($v);
                elseif(is_bool($v))
                    $part[] = $this->_clearBool($v);
                else
                    $part[] = $this->_clearString($v);

            }

            if(count($left) != count($part)) $this->_error("Got array with different numbers of items");
            $right[] = '('.join(',',$part).')';
            $i++;
        }
        return '('.join(',',$left).') VALUES '.join(',',$right);
    }

    private function _makeUpdate($value){
        if(!is_array($value)) $this->_error("Got non array value for <:i> placeholder");
        $set=array();
        foreach($value as $k=>$v){
            $key = $this->_clearName($k);

            if(is_numeric($v))
                $val = $this->_clearDigit($v);
            elseif(is_bool($v))
                $val = $this->_clearBool($v);
            else
                $val = $this->_clearString($v);

            $set[] = "$key=$val";
 
        }
   
        return 'SET '.join(',',$set);
    }

    private function _makePart($value){
        if(!$value instanceof DBPart) $this->_error("Got non Part value for <:p> placeholder");
        return $value->Part;
    }
}

class DBPart{
    public $Part;
    public function __construct($part=''){
        $this->Part = $part;
    }
}
