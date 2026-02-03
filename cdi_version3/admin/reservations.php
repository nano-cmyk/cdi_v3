<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirection si non connecté ou non admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

try {
    /* COPIER LE CODE ICI */
    
    // Traitement de l'annulation d'une réservation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
        if (!verifyCSRFToken($_POST['csrf_token'])) {
            throw new Exception("Token CSRF invalide");
        }
        
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        if ($reservation_id) {
            // On permet à l'admin d'annuler n'importe quelle réservation
            cancelReservation($reservation_id, null);
            $_SESSION['success_msg'] = "La réservation a été annulée avec succès";
            redirect('reservations.php');
        }
    }
    
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

displayHeader('Gestion des réservations');

if ($error_msg) {
    displayError($error_msg);
}
if ($success_msg) {
    displaySuccess($success_msg);
}
?>

<div class="card">
    <h2>Gestion des réservations</h2>
    
    <?php if (!empty($reservations)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Livre</th>
                        <th>Auteur</th>
                        <th>Utilisateur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['reservation_date'])); ?></td>
                            <td><?php echo htmlspecialchars($reservation['book_title']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['book_author']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['user_name']); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
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
        <p>Aucune réservation en cours.</p>
    <?php endif; ?>
</div>

<?php
displayFooter();
?>
