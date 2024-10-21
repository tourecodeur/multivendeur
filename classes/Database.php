<?php
class Database {
    private $conn;
    
    public function __construct() {
        $config = require_once '../config/database.php';
        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['username'],
                $config['password']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}?>