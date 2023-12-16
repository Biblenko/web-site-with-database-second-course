<?php

class DB {
    private $options = [];
    private $con = null;
    public function __construct($options) {
        $this->options = $options;
        $this->connect();
    }

    public function __destruct(){
        $this->close();
    }

    public function isConnect() {
        return ($this->con != null);
    }

    public function close() {
        if($this->con) mysqli_close($this->con);
    }

    public function connect() {
        $this->con = mysqli_connect($this->options['host'],$this->options['user'],$this->options['password'], $this->options['db']);
        return $this->isConnect();
    }
    public function query($sql) {
        if (!$this->isConnect()) return false;
        $r = mysqli_query($this->con, $sql);
        if (!$r) return false;
        $rows = [];
        while ($row = mysqli_fetch_assoc($r)) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function queryOne($sql) {
        if (!$this->isConnect()) return false;
        $r = mysqli_query($this->con, $sql);
        if (!$r) return false;
        return mysqli_fetch_assoc($r);
    }
}
