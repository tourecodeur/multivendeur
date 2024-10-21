<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-commerce Multivendeur</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Accueil</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="../public/login.php">Connexion</a>
                <a href="../public/register.php">Inscription</a>
            <?php else: ?>
                <a href="/cart.php">Panier</a>
                <?php if($_SESSION['user_type'] == 'vendor'): ?>
                    <a href="/vendor/dashboard.php">Dashboard Vendeur</a>
                <?php endif; ?>
                <a href="/logout.php">Déconnexion</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
<html>