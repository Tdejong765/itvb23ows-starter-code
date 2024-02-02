<?php

namespace Model;

class HiveModel{

    private $mysqli;

    public function __construct($host, $username, $password, $database, $port)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database, $port);

        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public function Execute($stmt){    

        if ($stmt === false) {
            die('Prepare Error: (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
        }

        $result = $stmt->execute();
        
        if ($result === false) {
            die('Execute Error: (' . $stmt->errno . ') ' . $stmt->error);
        }

        return $stmt;
    }

    public function dbRefresh($sql, $params){
        $stmt = $sql . $params;
        $query = $this->mysqli->prepare($stmt);
        return $this->Execute($query)->get_result();
    }

    public function dbRestart($sql){   
        $query = $this->mysqli->prepare($sql);
        return $this->Execute($query)->insert_id;
    }

    public function dbPass($sql, $param1, $param2, $param3){
        $stmt = $this->mysqli->prepare($sql);   
        $stmt->bind_param('iis', $param1, $param2, $param3);
        return $this->Execute($stmt)->insert_id;
    }

    public function dbPlay($sql, $param1, $param2, $param3, $param4, $param5){
        $stmt = $this->mysqli->prepare($sql);   
        $stmt->bind_param('issis', $param1, $param2, $param3, $param4, $param5);
        return $this->Execute($stmt)->insert_id;
    }

    public function dbUndo($sql, $params){
        $stmt = $sql . $params;
        $query = $this->mysqli->prepare($stmt);
        return $this->Execute($query)->get_result()->fetch_array();
    }
    
    public function dbMove($sql, $param1, $param2, $param3, $param4, $param5){
        $stmt = $this->mysqli->prepare($sql);   
        $stmt->bind_param('issis', $param1, $param2, $param3, $param4, $param5);
        return $this->Execute($stmt)->insert_id;
    }
}