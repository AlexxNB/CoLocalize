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
           //     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ];

            try {
                $GLOBALS['DBConnected'] = new PDO($pdo_mysql, $this->_user, $this->_password, $options);
            } catch (PDOException $e) {
                $this->_printError($e->getMessage());
            }
        }
    }

    public function insert($table, $cols){
        $c = '';
        $v = '';
        if(is_array($cols))
        {
            foreach($cols as $col=>$value)
            {
                $c .= "$col,";
                $value = str_replace("'","\'",$value);
                $v .= "'$value',";
            }
            $c = substr($c,0,-1);
            $v = substr($v,0,-1);

            $query = "INSERT INTO $table ($c) VALUES ($v)";

            $this->_execute($query);
            return $this->lastID();
        }
    }

    public function delete($table, $where=''){
        $query = "DELETE FROM $table";
        if(!empty($where)) $query .= " WHERE $where";

        $this->_execute($query);
    }

    public function update($table, $cols, $where=''){
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

    public function lastID(){
        return $GLOBALS['DBConnected']->lastInsertId();
    }

    public function selectCell($table, $cell, $where='', $order=''){
        $this->select($table, $where, $order, $cell);

        if($this->_emptyResult())
            return false;

        $c = $this->_fetch();
        return $c[$cell];
    }

    public function selectRow($table, $where=''){
        $this->select($table, $where);

        if($this->_emptyResult())
            return false;
        else
            return $this->_fetch();
    }

    public function numRows(){
        return $this->_numRows();
    }

    public function selectInArray($table, $where='', $order='', $cols='*'){
        $this->select($table, $where, $order, $cols);

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

    public function select($table, $where='', $order='', $cols='*'){
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

        $this->query($query);
    }

    public function fetchInArray(){
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

    public function query($query){
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
        if(self::DEBUG)
        {
            echo '<div style="position:absolute; width:100%; top:0px; left:0px; background-color:black; color:#33CC00; font-family:courier; font-size:12px; border: 2px solid #33CC00">';
            if ($query) echo '<b>Обнаружена ошибка в SQL запросе:</b><br />&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $query, '<br />&nbsp;<br />';
            echo $error, '</div>';
        }
    }

    public function close(){
        $GLOBALS['DBConnected'] = null;
    }
}
