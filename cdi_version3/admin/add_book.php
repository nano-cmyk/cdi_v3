<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirection si non connecté ou non admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error_msg = "Token CSRF invalide";
    } else {
        try {
            /* COPIER LE CODE ICI*/
            
            $_SESSION['success_msg'] = "Le livre a été ajouté avec succès";
            redirect('../books.php');
            
        } catch (PDOException $e) {
            $error_msg = "Erreur lors de l'ajout du livre";
        }
    }
}

displayHeader('Ajouter un livre');

if ($error_msg) {
    displayError($error_msg);
}
?>

<div class="card">
    <h2>Ajouter un livre</h2>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="title" class="form-label">Titre</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="author" class="form-label">Auteur</label>
            <input type="text" id="author" name="author" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" id="isbn" name="isbn" class="form-control">
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="../books.php" class="btn btn-danger">Annuler</a>
    </form>
</div>

<?php
displayFooter();
?>
