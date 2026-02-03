<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

displayHeader('Accueil');

if ($error_msg) {
    displayError($error_msg);
}
if ($success_msg) {
    displaySuccess($success_msg);
}
?>

<div class="card">
    <h1 class="text-center">Bienvenue au CDI - Version 3</h1>
    
    <?php if (!isLoggedIn()): ?>
        <p class="text-center">
            Cette version propose un système complet de gestion de bibliothèque avec :<br>
            - Authentification sécurisée<br>
            - Protection contre les injections SQL<br>
            - Système de réservation de livres
        </p>
        <p class="text-center">
            Veuillez vous <a href="login.php">connecter</a> ou vous <a href="register.php">inscrire</a> pour accéder aux livres.
        </p>
    <?php else: ?>
        <p class="text-center">
            Bonjour <?php echo htmlspecialchars($_SESSION['username']); ?> !
        </p>
        <div class="text-center">
            <a href="books.php" class="btn btn-primary">Voir les livres</a>
            <a href="my_reservations.php" class="btn btn-success">Mes réservations</a>
        </div>
    <?php endif; ?>
</div>

<?php
displayFooter();
?>
