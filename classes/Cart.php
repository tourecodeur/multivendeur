// classes/Cart.php
<?php
class Cart {
    public function addItem($product_id, $quantity) {
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function removeItem($product_id) {
        if(isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    public function getTotal() {
        $total = 0;
        if(isset($_SESSION['cart'])) {
            foreach($_SESSION['cart'] as $product_id => $quantity) {
                // Get product price from database and calculate
                // This is simplified for the example
                $product = new Product($this->db);
                $details = $product->getProductDetails($product_id);
                $total += $details['prix'] * $quantity;
            }
        }
        return $total;
    }
}?>