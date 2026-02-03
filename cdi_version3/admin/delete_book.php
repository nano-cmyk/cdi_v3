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
    
    // Vérification si le livre a des réservations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE book_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Impossible de supprimer ce livre car il a des réservations actives");
    }
    
    // Suppression du livre
    /* COPIER LE CODE ICI */
    
    $_SESSION['success_msg'] = "Le livre a été supprimé avec succès";
    
} catch (Exception $e) {
    $_SESSION['error_msg'] = $e->getMessage();
}

redirect('../books.php');
?>
