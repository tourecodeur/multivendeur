<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $user = new User($db->getConnection());
    
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'user_type' => $_POST['user_type']
    ];
    
    if ($user->register($data)) {
        header('Location: /login.php');
        exit;
    } else {
        $error = "Erreur lors de l'inscription";
    }
}
?>

<div class="register-container">
    <h2>Inscription</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Type de compte</label>
            <select name="user_type" required class="form-control">
                <option value="client">Client</option>
                <option value="vendor">Vendeur</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>