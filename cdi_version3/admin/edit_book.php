<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirection si non connecté ou non admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception("ID de livre invalide");
    }
    
    // Récupération du livre
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        throw new Exception("Livre non trouvé");
    }
    
    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Token CSRF invalide");
        }
        
        /* COPIER LE CODE ICI */
        
        $_SESSION['success_msg'] = "Le livre a été modifié avec succès";
        redirect('../books.php');
    }
    
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

displayHeader('Modifier un livre');

if ($error_msg) {
    displayError($error_msg);
}
?>

<div class="card">
    <h2>Modifier un livre</h2>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="title" class="form-label">Titre</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="author" class="form-label">Auteur</label>
            <input type="text" id="author" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
        </div>
        <div class="form-group">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" id="isbn" name="isbn" class="form-control" value="<?php echo htmlspecialchars($book['isbn']); ?>">
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($book['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="../books.php" class="btn btn-danger">Annuler</a>
    </form>
</div>

<?php
displayFooter();
?>
