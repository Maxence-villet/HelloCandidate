<?php
class Database {
    private $host = 'hellocandidate-db-1';
    private $db_name = 'HelloCandidate';
    private $username = 'root';
    private $password = 'mariadb';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Erreur de connexion : " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
        return $this->conn;
    }
}