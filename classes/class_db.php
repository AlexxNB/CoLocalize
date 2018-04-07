<?php

class DB{
    const DEBUG = true;

    private $_host;
    private $_user;
    private $_password;
    private $_database;
    private $_port;
    private $_result;

    public function __construct(){
        $this->_loadConfig();
        $pdo_mysql = 'mysql:host='. $this->_host .';dbname='. $this->_database .';port='. $this->_port;

        if(!isset($GLOBALS['DBConnected']))
        {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ];

            try {
                $GLOBALS['DBConnected'] = new PDO($pdo_mysql, $this->_user, $this->_password, $options);
            } catch (PDOException $e) {
                $this->_printError($e->getMessage());
            }
        }
    }

    public function Insert($table, $cols, $ignore=false){
        $c = '';
        $v = '';
        if(is_array($cols))
        {
            if(is_array($cols[0])) 
                $lines = $cols;
            else
                $lines = array($cols);

            $values = array();
            $c = array();
            $i=0;
            foreach($lines as $line){
                $v = array();
                foreach($line as $col=>$value){
                    if($i==0) $c[] = $col;
                    $v[] = "'".str_replace("'","\'",$value)."'";
                }
                $i++;

                if(count($c) != count($v)) continue;
                
                $values[] = "(".join(',',$v).")";
            }
            $cols = "(".join(',',$c).")";
            $vals = join(',',$values);

            if($ignore)
                $query = "INSERT IGNORE INTO $table $cols VALUES $vals";
            else
                $query = "INSERT INTO $table $cols VALUES $vals";
                
            $this->_execute($query);
            return $this->_lastID();
        }
        return false;
    }

    public function Delete($table, $where=''){
        $query = "DELETE FROM $table";
        if(!empty($where)) $query .= " WHERE $where";

        return $this->_execute($query);
    }

    public function Update($table, $cols, $where=''){
        $c = '';
        if(is_array($cols))
        {
            foreach($cols as $col=>$value)
            {
                $value = str_replace("'","\'",$value);
                $c .= "$col='$value',";
            }
            $c = substr($c,0,-1);

            $query = "UPDATE $table SET $c";
            if(!empty($where)) $query .= " WHERE $where";

            $this->_execute($query);
            return true;
        }
        else return false;
    }

    public function _lastID(){
        return $GLOBALS['DBConnected']->lastInsertId();
    }

    public function SelectCell($table, $cell, $where='', $order=''){
        $this->Select($table, $where, $order, $cell);

        if($this->_emptyResult())
            return false;

        $c = $this->_fetch();
        return $c[$cell];
    }

    public function SelectRow($table, $where=''){
        $this->Select($table, $where);

        if($this->_emptyResult())
            return false;
        else
            return $this->_fetch();
    }

    public function NumRows(){
        return $this->_numRows();
    }

    public function SelectNum($table,$where=''){
        $query = "SELECT COUNT(1) as num FROM $table";
        if(!empty($where)) $query .= " WHERE $where";
        $this->Query($query);
        $res = $this->_fetch();
        return $res['num'];
    }

    public function SelectInArray($table, $where='', $order='', $cols='*'){
        $this->Select($table, $where, $order, $cols);

        if($this->_emptyResult())
            return false;
        else
        {
            $result = array();
            while($line = $this->_fetch())
            {
                $result[] = $line;
            }

            return $result;
        }
    }

    public function Select($table, $where='', $order='', $cols='*'){
        if(is_array($cols))
        {
            $c = '';
            foreach($cols as $col)
            {
                $c .= "$col,";
            }
            $c = substr($c, 0, -1);
        }
        else
            $c = $cols;

        $query = "SELECT $c FROM $table";

        if(!empty($where)) $query .= " WHERE $where";
        if(!empty($order)) $query .= " ORDER BY $order";

        $this->Query($query);
    }

    public function FetchInArray(){
        if($this->_emptyResult())
            return false;
        else
        {
            $result = array();
            while($line = $this->_fetch())
            {
                $result[] = $line;
            }

            return $result;
        }
    }

    public function Query($query){
        $this->_result = $this->_execute($query);
    }

    private function _fetch(){
        return $this->_result->fetch();
    }

    private function _numRows(){
        return $this->_result->rowCount();
    }

    private function _emptyResult(){
        if($this->_numRows() == 0) return true;
        else return false;
    }

    private function _execute($query){
        $result = '';

        try {
            $result = $GLOBALS['DBConnected']->query($query);
        } catch (PDOException $e) {
            $this->_printError($e->getMessage(), $query);
        }

        return $result;
    }

    private function _loadConfig(){
        require(dirname(__FILE__)."/../_config.php");
        $this->_host = $server;
        $this->_user = $user;
        $this->_password = $password;
        $this->_database = $database;
        $this->_port = $port;
    }

    private function _printError($error, $query = ''){
        if(self::DEBUG){
            echo $error;
            echo "\r\n";
            echo $query;
        }else{
            header("HTTP/1.0 500 Internal Server Error");
        }
        exit();
    }

    public function close(){
        $GLOBALS['DBConnected'] = null;
    }
}
