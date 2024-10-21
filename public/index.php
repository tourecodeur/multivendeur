<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Product.php';

$db = new Database();
$product = new Product($db->getConnection());
$products = $product->getProducts();
?>

<div class="products-grid">
    <?php foreach($products as $product): ?>
        <div class="product-card">
            <img src="/assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>">
            <h3><?php echo $product['nom']; ?></h3>
            <p><?php echo $product['description']; ?></p>
            <p class="price"><?php echo number_format($product['prix'], 2); ?> €</p>
            <form action="/cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit">Ajouter au panier</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>