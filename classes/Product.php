<?php
class Product {
    private $db;
    private $table = "products";

    public function __construct($db) {
        $this->db = $db;
    }

    public function addProduct($data) {
        $query = "INSERT INTO {$this->table} 
                 (nom, description, prix, stock, vendor_id, categorie) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['stock'],
            $data['vendor_id'],
            $data['categorie']
        ]);
    }

    public function getProducts($vendor_id = null) {
        $query = "SELECT * FROM {$this->table}";
        if($vendor_id) {
            $query .= " WHERE vendor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$vendor_id]);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}?>