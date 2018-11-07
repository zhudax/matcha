<?php

class Database {
    private $pdo;
    private $sql;
    private $result;

    public function __construct() {
        try
		{
			$this->pdo = new PDO("mysql:host=localhost;dbname=matcha", "root", "password");
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo "Failed to connect to database " . $e->getMessage() . "<br>";
		}
    }

    public function execSql($requete, $value = array()) {
        $this->sql = $this->pdo->prepare($requete);
        $this->sql->execute($value);
    }

    public function getAllResult() {
        $this->result = $this->sql->fetchAll();
        $this->sql->closeCursor();
        if (count($this->result) > 0)
            return ($this->result);
        else
            return (0);
    }

    public function getFirstResult() {
        $this->result = $this->sql->fetchAll();
        $this->sql->closeCursor();
        if (count($this->result) > 0)
            return ($this->result[0]);
        else
            return (0);
    }

    public function setCle($uid) {
        $cle = hash("whirlpool", microtime(TRUE) * 100000);
        $sqlNewCle = "UPDATE `users` SET `cle`=? WHERE `id`=?";
        $this->execSql($sqlNewCle, array($cle, $uid));
    }
}
