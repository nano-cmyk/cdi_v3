<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirection si non connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

try {
    // Récupération des livres avec leur statut
    $stmt = $pdo->prepare("
        SELECT b.*, 
               CASE WHEN r.id IS NOT NULL THEN 'reserve' ELSE b.status END as current_status,
               r.user_id as reserved_by
        FROM books b
        LEFT JOIN reservations r ON b.id = r.book_id
        ORDER BY b.created_at DESC
    ");
    $stmt->execute();
    $books = $stmt->fetchAll();
    
    // Traitement d'une nouvelle réservation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve_book'])) {
        if (!verifyCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Token CSRF invalide");
        }
        
        $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
        if ($book_id) {
            reserveBook($book_id, $_SESSION['user_id']);
            $_SESSION['success_msg'] = "Le livre a été réservé avec succès";
            redirect('books.php');
        }
    }
    
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

displayHeader('Liste des livres');

if ($error_msg) {
    displayError($error_msg);
}
if ($success_msg) {
    displaySuccess($success_msg);
}
?>

<div class="card">
    <h2>Liste des livres</h2>
    
    <?php if (isAdmin()): ?>
        <a href="admin/add_book.php" class="btn btn-primary mb-3">Ajouter un livre</a>
    <?php endif; ?>
    
    <?php if (!empty($books)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                            <td>
                                <?php if ($book['current_status'] === 'disponible'): ?>
                                    <span class="badge badge-success">Disponible</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Réservé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($book['current_status'] === 'disponible'): ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" name="reserve_book" class="btn btn-success btn-sm">Réserver</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (isAdmin()): ?>
                                    <a href="admin/edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                    <a href="admin/delete_book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Aucun livre disponible pour le moment.</p>
    <?php endif; ?>
</div>

<?php
displayFooter();
?>
