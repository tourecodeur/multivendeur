// admin/dashboard.php
<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/Product.php';
require_once '../classes/Vendor.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$order = new Order($db->getConnection());
$product = new Product($db->getConnection());
$vendor = new Vendor($db->getConnection());

$totalOrders = $order->getTotalOrders();
$totalProducts = $product->getTotalProducts();
$totalVendors = $vendor->getTotalVendors();
$recentOrders = $order->getRecentOrders();
$pendingVendors = $vendor->getPendingVendors();
?>

<div class="admin-dashboard">
    <h1>Dashboard Administrateur</h1>
    
    <div class="stats-container">
        <div class="stat-card">
            <h3>Commandes</h3>
            <p class="number"><?php echo $totalOrders; ?></p>
            <p class="label">Total des commandes</p>
        </div>
        <div class="stat-card">
            <h3>Produits</h3>
            <p class="number"><?php echo $totalProducts; ?></p>
            <p class="label">Produits en ligne</p>
        </div>
        <div class="stat-card">
            <h3>Vendeurs</h3>
            <p class="number"><?php echo $totalVendors; ?></p>
            <p class="label">Vendeurs actifs</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="recent-orders">
            <h2>Commandes récentes</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo $order['client_nom']; ?></td>
                        <td><?php echo number_format($order['total_ttc'], 2); ?> €</td>
                        <td><span class="status-badge status-<?php echo $order['statut']; ?>"><?php echo $order['statut']; ?></span></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['date_creation'])); ?></td>
                        <td>
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="orders.php" class="btn btn-link">Voir toutes les commandes</a>
        </div>

        <div class="pending-vendors">
            <h2>Vendeurs en attente d'approbation</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Boutique</th>
                        <th>Email</th>
                        <th>Date demande</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingVendors as $vendor): ?>
                    <tr>
                        <td>#<?php echo $vendor['id']; ?></td>
                        <td><?php echo $vendor['nom_boutique']; ?></td>
                        <td><?php echo $vendor['email']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($vendor['date_creation'])); ?></td>
                        <td>
                            <a href="vendor-details.php?id=<?php echo $vendor['id']; ?>" class="btn btn-sm btn-primary">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="vendors.php" class="btn btn-link">Voir tous les vendeurs</a>
        </div>
    </div>
</div>

// admin/products.php
<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$product = new Product($db->getConnection());
$category = new Category($db->getConnection());

// Gestion des filtres
$filters = [
    'category' => $_GET['category'] ?? null,
    'vendor' => $_GET['vendor'] ?? null,
    'status' => $_GET['status'] ?? null,
    'search' => $_GET['search'] ?? null
];

// Pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$products = $product->getFilteredProducts($filters, $page, $perPage);
$totalProducts = $product->getTotalFilteredProducts($filters);
$totalPages = ceil($totalProducts / $perPage);

// Liste des catégories pour le filtre
$categories = $category->getAllCategories();
?>

<div class="admin-products">
    <div class="page-header">
        <h1>Gestion des Produits</h1>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exportModal">
                Exporter
            </button>
        </div>
    </div>

    <div class="filters-container">
        <form method="GET" class="filters-form">
            <div class="form-group">
                <input type="text" name="search" value="<?php echo $filters['search']; ?>" placeholder="Rechercher..." class="form-control">
            </div>
            <div class="form-group">
                <select name="category" class="form-control">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['nom']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="actif" <?php echo $filters['status'] == 'actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactif" <?php echo $filters['status'] == 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="en_rupture" <?php echo $filters['status'] == 'en_rupture' ? 'selected' : ''; ?>>En rupture</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Vendeur</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td>#<?php echo $product['id']; ?></td>
                    <td>
                        <img src="/assets/images/products/<?php echo $product['images'][0]; ?>" alt="<?php echo $product['nom']; ?>" class="product-thumbnail">
                    </td>
                    <td><?php echo $product['nom']; ?></td>
                    <td><?php echo $product['categorie_nom']; ?></td>
                    <td><?php echo $product['vendor_nom']; ?></td>
                    <td><?php echo number_format($product['prix'], 2); ?> €</td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><span class="status-badge status-<?php echo $product['statut']; ?>"><?php echo $product['statut']; ?></span></td>
                    <td>
                        <div class="btn-group">
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">Supprimer</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>" 
           class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal d'exportation -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter les produits</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="export-products.php" method="POST">
                    <div class="form-group">
                        <label>Format</label>
                        <select name="format" class="form-control">
                            <option value="csv">CSV</option>
                            <option value="xlsx">Excel</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Exporter</button>
                </form>
            </div>
        </div>
    </div>
</div>

// admin/vendors.php
<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/Vendor.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$vendor = new Vendor($db->getConnection());

// Gestion des filtres
$filters = [
    'status' => $_GET['status'] ?? null,
    'search' => $_GET['search'] ?? null
];

// Pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$vendors = $vendor->getFilteredVendors($filters, $page, $perPage);
$totalVendors = $vendor->getTotalFilteredVendors($filters);
$totalPages = ceil($totalVendors / $perPage);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'approve':
            $vendor->approveVendor($vendor_id);
            break;
        case 'reject':
            $vendor->rejectVendor($vendor_id);
            break;
        case 'suspend':
            $vendor->suspendVendor($vendor_id);
            break;
        case 'activate':
            $vendor->activateVendor($vendor_id);
            break;
    }
    
    // Redirection pour éviter la resoumission du formulaire
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($filters));
    exit;
}
?>

<div class="admin-vendors">
    <div class="page-header">
        <h1>Gestion des Vendeurs</h1>
    </div>

    <div class="filters-container">
        <form method="GET" class="filters-form">
            <div class="form-group">
                <input type="text" name="search" value="<?php echo $filters['search']; ?>" placeholder="Rechercher..." class="form-control">
            </div>
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" <?php echo $filters['status'] == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="approuve" <?php echo $filters['status'] == 'approuve' ? 'selected' : ''; ?>>Approuvé</option>
                    <option value="rejete" <?php echo $filters['status'] == 'rejete' ? 'selected' : ''; ?>>Rejeté</option>
                    <option value="suspendu" <?php echo $filters['status'] == 'suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Boutique</th>
                    <th>Vendeur</th>
                    <th>Email</th>
                    <th>Produits</th>
                    <th>Commandes</th>
                    <th>Statut</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                <tr>
                    <td>#<?php echo $vendor['id']; ?></td>
                    <td><?php echo $vendor['nom_boutique']; ?></td>
                    <td><?php echo $vendor