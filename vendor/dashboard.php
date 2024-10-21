// vendor/dashboard.php
<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Vendor.php';
require_once '../classes/Product.php';
require_once '../classes/Order.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$vendor = new Vendor($db->getConnection());
$product = new Product($db->getConnection());
$order = new Order($db->getConnection());

$vendorDetails = $vendor->getVendorDetails($_SESSION['user_id']);
$products = $product->getProducts($_SESSION['user_id']);
$orders = $order->getOrders($_SESSION['user_id']);
?>

<div class="vendor-dashboard">
    <h1>Dashboard Vendeur</h1>
    
    <section class="vendor-stats">
        <div class="stat-card">
            <h3>Produits</h3>
            <p><?php echo count($products); ?></p>
        </div>
        <div class="stat-card">
            <h3>Commandes</h3>
            <p><?php echo count($orders); ?></p>
        </div>
    </section>

    <section class="recent-orders">
        <h2>Commandes récentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_id']; ?></td>
                        <td><?php echo number_format($order['total'], 2); ?> €</td>
                        <td><?php echo $order['statut']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php require_once '../includes/footer.php'; ?>