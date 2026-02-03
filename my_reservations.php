<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirection si non connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

try {
    // Si admin, récupère toutes les réservations, sinon uniquement celles de l'utilisateur
    if (isAdmin()) {
        $stmt = $pdo->prepare("
            SELECT r.id as reservation_id, 
                   b.*,
                   u.username as user_name,
                   r.reservation_date
            FROM reservations r 
            JOIN books b ON r.book_id = b.id 
            JOIN users u ON r.user_id = u.id
            ORDER BY r.reservation_date DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT r.id as reservation_id, 
                   b.*,
                   u.username as user_name,
                   r.reservation_date
            FROM reservations r 
            JOIN books b ON r.book_id = b.id 
            JOIN users u ON r.user_id = u.id
            WHERE r.user_id = ? 
            ORDER BY r.reservation_date DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $reservations = $stmt->fetchAll();
    
    // Traitement de l'annulation d'une réservation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
        if (!verifyCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Token CSRF invalide");
        }
        
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        if ($reservation_id) {
            // Si admin, peut annuler n'importe quelle réservation
            cancelReservation($reservation_id, isAdmin() ? null : $_SESSION['user_id']);
            $_SESSION['success_msg'] = "La réservation a été annulée avec succès";
            redirect('my_reservations.php');
        }
    }
    
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

displayHeader(isAdmin() ? 'Gestion des réservations' : 'Mes réservations');

if ($error_msg) {
    displayError($error_msg);
}
if ($success_msg) {
    displaySuccess($success_msg);
}
?>

<div class="card">
    <h2><?php echo isAdmin() ? 'Gestion des réservations' : 'Mes réservations'; ?></h2>
    
    <?php if (!empty($reservations)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <?php if (isAdmin()): ?>
                            <th>Date réservation</th>
                            <th>Réservé par</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['title']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['author']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['isbn']); ?></td>
                            <?php if (isAdmin()): ?>
                                <td><?php echo date('d/m/Y H:i', strtotime($reservation['reservation_date'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['user_name']); ?></td>
                            <?php endif; ?>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                    <button type="submit" name="cancel_reservation" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                        Annuler la réservation
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Vous n'avez aucune réservation en cours.</p>
    <?php endif; ?>
    
    <a href="books.php" class="btn btn-primary">Voir les livres disponibles</a>
</div>

<?php
displayFooter();
?>
