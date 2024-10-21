<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Product.php';

$db = new Database();
$cart = new Cart($db->getConnection());
$product = new Product($db->getConnection());

// Gestion des actions du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $cart->addItem($_POST['product_id'], $_POST['quantity']);
                break;
            case 'update':
                $cart->updateItem($_POST['product_id'], $_POST['quantity']);
                break;
            case 'remove':
                $cart->removeItem($_POST['product_id']);
                break;
        }
    }
}

$cartItems = $cart->getItems();
$total = $cart->getTotal();
?>

<div class="cart-container">
    <h2>Mon Panier</h2>
    
    <?php if (empty($cartItems)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="/assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['nom']; ?>">
                    <div class="item-details">
                        <h3><?php echo $item['nom']; ?></h3>
                        <p class="price"><?php echo number_format($item['prix'], 2); ?> €</p>
                        
                        <form method="POST" class="quantity-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                            <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                        </form>
                        
                        <form method="POST" class="remove-form">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <h3>Récapitulatif</h3>
            <p>Total : <strong><?php echo number_format($total, 2); ?> €</strong></p>
            <a href="/checkout.php" class="btn btn-success">Procéder au paiement</a>
        </div>
    <?php endif; ?>
</div>