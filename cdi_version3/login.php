<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    redirect('index.php');
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error_msg = "Token CSRF invalide";
    } else {
        try {
            $username = trim($_POST['username']);
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Debug détaillé
            error_log("Tentative de connexion pour l'utilisateur: " . $username);
            error_log("Hash stocké: " . ($user ? $user['password'] : 'utilisateur non trouvé'));
            error_log("Mot de passe fourni: " . $_POST['password']);
            error_log("Longueur du mot de passe: " . strlen($_POST['password']));
            error_log("Hex du mot de passe: " . bin2hex($_POST['password']));
            
            // Debug du résultat de password_verify
            $verify_result = password_verify($_POST['password'], $user['password']);
            error_log("Résultat de password_verify: " . ($verify_result ? "true" : "false"));
            
            if ($user && $verify_result) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // Régénère l'ID de session pour éviter la fixation de session
                session_regenerate_id(true);
                
                redirect('index.php');
            } else {
                $error_msg = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $error_msg = "Erreur lors de la connexion";
        }
    }
}

displayHeader('Connexion');

if ($error_msg) {
    displayError($error_msg);
}
?>

<div class="card">
    <h2>Connexion</h2>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <p class="mt-3">
        Pas encore de compte ? <a href="register.php">Inscrivez-vous</a>
    </p>
</div>

<?php
displayFooter();
?>
