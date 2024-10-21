<?php
require_once '../includes/header.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $user = new User($db->getConnection());
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($user->login($email, $password)) {
        header('Location: /');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<div class="login-container">
    <h2>Connexion</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    
    <p>Pas encore de compte ? <a href="public/register.php">S'inscrire</a></p>
</div>