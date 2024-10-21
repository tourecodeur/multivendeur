// classes/Order.php
<?php
class Order {
    private $db;
    private $table = "orders";

    public function __construct($db) {
        $this->db = $db;
    }

    public function createOrder($data) {
        $query = "INSERT INTO {$this->table} 
                 (user_id, total, statut) 
                 VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['total'],
            'en_attente'
        ]);
    }

    public function getOrders($vendor_id = null) {
        $query = "SELECT o.*, od.* FROM {$this->table} o 
                 JOIN order_details od ON o.id = od.order_id";
        if($vendor_id) {
            $query .= " WHERE od.vendor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$vendor_id]);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}?>