<?php

class Database {
    private $host = 'srv1575.hstgr.io';
    private $db_name = 'u564798502_cllaguarima';
    private $username = 'u564798502_cllaguarima';
    private $password = 'Cllaguarima.2025';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
