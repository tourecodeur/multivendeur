<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Review.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header('Location: /');
    exit;
}

$db = new Database();
$product = new Product($db->getConnection());
$review = new Review($db->getConnection());

$productDetails = $product->getProductDetails($product_id);
$reviews = $review->getProductReviews($product_id);
?>

<div class="product-details">
    <div class="product-images">
        <?php foreach (json_decode($productDetails['images']) as $image): ?>
            <img src="/assets/images/products/<?php echo $image; ?>" alt="<?php echo $productDetails['nom']; ?>">
        <?php endforeach; ?>
    </div>
    
    <div class="product-info">
        <h1><?php echo $productDetails['nom']; ?></h1>
        <p class="price">
            <?php if ($productDetails['prix_promo']): ?>
                <span class="original-price"><?php echo number_format($productDetails['prix'], 2); ?> €</span>
                <span class="promo-price"><?php echo number_format($productDetails['prix_promo'], 2); ?> €</span>
            <?php else: ?>
                <span class="current-price"><?php echo number_format($productDetails['prix'], 2); ?> €</span>
            <?php endif; ?>
        </p>
        
        <div class="description">
            <?php echo $productDetails['description']; ?>
        </div>
        
        <form action="/cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="quantity">
                <label>Quantité</label>
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $productDetails['stock']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Ajouter au panier</button>
        </form>
    </div>
    
    <div class="reviews">
        <h2>Avis clients</h2>
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= $review['note'] ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p class="comment"><?php echo $review['commentaire']; ?></p>
                <p class="author">Par <?php echo $review['nom']; ?> le <?php echo date('d/m/Y', strtotime($review['date_creation'])); ?></p>
            </div>
        <?php endforeach; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="/add-review.php">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <div class="form-group">
                    <label>Note</label>
                    <select name="note" required>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> étoile(s)</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter un avis</button>
            </form>
        <?php endif; ?>
    </div>
</div>