<?php
class Vendor {
    private $db;
    private $table = "vendors";

    public function __construct($db) {
        $this->db = $db;
    }

    public function getVendorDetails($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($data) {
        $query = "UPDATE {$this->table} 
                 SET nom_boutique = ?, description = ?, adresse = ? 
                 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['nom_boutique'],
            $data['description'],
            $data['adresse'],
            $data['id']
        ]);
    }
}?>