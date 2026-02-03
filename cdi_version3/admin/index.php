<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirection si non connecté ou non admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

try {
    // Récupération des statistiques
    $stats = [
        'total_books' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
        'reserved_books' => $pdo->query("SELECT COUNT(*) FROM books WHERE status = 'reserve'")->fetchColumn(),
        'total_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn(),
        'active_reservations' => $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn()
    ];
} catch (PDOException $e) {
    $error_msg = "Erreur lors de la récupération des statistiques";
}

displayHeader('Administration');

if ($error_msg) {
    displayError($error_msg);
}
?>

<div class="card">
    <h2>Tableau de bord d'administration</h2>
    
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                <h3>Statistiques</h3>
                <ul>
                    <li>Nombre total de livres : <?php echo $stats['total_books']; ?></li>
                    <li>Livres réservés : <?php echo $stats['reserved_books']; ?></li>
                    <li>Nombre d'utilisateurs : <?php echo $stats['total_users']; ?></li>
                    <li>Réservations actives : <?php echo $stats['active_reservations']; ?></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col">
            <h3>Actions</h3>
            <a href="add_book.php" class="btn btn-primary">Ajouter un livre</a>
            <a href="../books.php" class="btn btn-primary">Gérer les livres</a>
            <a href="reservations.php" class="btn btn-success">Voir toutes les réservations</a>
        </div>
    </div>
</div>

<?php
displayFooter();
?>
