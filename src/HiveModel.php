<?php

class hiveModel{

    private $mysqli;

    public function __construct($host, $username, $password, $database, $port)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database, $port);

        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }


    function getState() {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }


    function setState($state) {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    public function prepareAndExecute($sql, $params){
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare Error: (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
        }

        $stmt->bind_param(...$params);

        $result = $stmt->execute();

        if ($result === false) {
            die('Execute Error: (' . $stmt->errno . ') ' . $stmt->error);
        }

        return $result;
    }
}