<?php

class HiveModel{

    private $mysqli;

    public function __construct($host, $username, $password, $database, $port)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database, $port);

        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public function prepareAndExecute($sql, $params){
        $query = $sql . $params;
        $stmt = $this->mysqli->prepare($query);

        if ($stmt === false) {
            die('Prepare Error: (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
        }

        $result = $stmt->execute();
        
        if ($result === false) {
            die('Execute Error: (' . $stmt->errno . ') ' . $stmt->error);
        }

        $stmt->insert_id;

        return $stmt->get_result();
    }
}