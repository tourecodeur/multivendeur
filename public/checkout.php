<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$cart = new Cart($db->getConnection());
$order = new Order($db->getConnection());

$cartItems = $cart->getItems();
if (empty($cartItems)) {
    header('Location: /cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Création de la commande
    $orderData = [
        'user_id' => $_SESSION['user_id'],
        'adresse_livraison' => $_POST['adresse_livraison'],
        'adresse_facturation' => $_POST['adresse_facturation'],
        'mode_paiement' => $_POST['mode_paiement'],
        'items' => $cartItems,
        'total' => $cart->getTotal()
    ];
    
    if ($order->createOrder($orderData)) {
        $cart->clear();
        header('Location: /order-confirmation.php');
        exit;
    }
}
?>

<div class="checkout-container">
    <h2>Finalisation de la commande</h2>
    
    <div class="checkout-steps">
        <div class="step active">1. Adresse</div>
        <div class="step">2. Paiement</div>
        <div class="step">3. Confirmation</div>
    </div>
    
    <form method="POST" action="" class="checkout-form">
        <div class="addresses">
            <div class="shipping-address">
                <h3>Adresse de livraison</h3>
                <textarea name="adresse_livraison" required></textarea>
            </div>
            
            <div class="billing-address">
                <h3>Adresse de facturation</h3>
                <div class="form-check">
                    <input type="checkbox" id="same_address" checked>
                    <label for="same_address">Identique à l'adresse de livraison</label>
                </div>
                <textarea name="adresse_facturation"></textarea>
            </div>
        </div>
        
        <div class="payment">
            <h3>Mode de paiement</h3>
            <div class="payment-methods">
                <div class="form-check">
                    <input type="radio" name="mode_paiement" value="carte" id="carte" required>
                    <label for="carte">Carte bancaire</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="mode_paiement" value="paypal" id="paypal">
                    <label for="paypal">PayPal</label>
                </div>
            </div>
        </div>
        
        <div class="order-summary">
            <h3>Récapitulatif de la commande</h3>
            <?php foreach ($cartItems as $item): ?>
                <div class="order-item">
                    <span><?php echo $item['nom']; ?></span>
                    <span><?php echo $item['quantity']; ?> x <?php echo number_format($item['prix'], 2); ?> €</span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <strong>Total</strong>
                <strong><?php echo number_format($cart->getTotal(), 2); ?> €</strong>
            </div>
        </div>
        
        <button type="submit" class="btn btn-success">Confirmer la commande</button>
    </form>
</div>