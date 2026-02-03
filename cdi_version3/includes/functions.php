<?php
// Fonctions communes

function displayError($message) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}

function displaySuccess($message) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

function redirect($page) {
    header('Location: ' . $page);
    exit();
}

// Fonction pour nettoyer les entrées utilisateur
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Fonction pour afficher l'en-tête HTML
function displayHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> - CDI (v3)</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="navbar-brand">CDI v3</a>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li><a href="books.php" class="nav-link">Livres</a></li>
                        <li><a href="my_reservations.php" class="nav-link"><?php echo isAdmin() ? 'Gestion réservations' : 'Mes réservations'; ?></a></li>
                        <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="nav-link">Connexion</a></li>
                        <li><a href="register.php" class="nav-link">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <div class="container mt-3">
    <?php
}

// Fonction pour afficher le pied de page HTML
function displayFooter() {
    ?>
        </div>
    </body>
    </html>
    <?php
}

// Fonction pour vérifier si un livre est disponible
function isBookAvailable($book_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT status FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    return $book && $book['status'] === 'disponible';
}

// Fonction pour réserver un livre
function reserveBook($book_id, $user_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Vérifie si le livre est toujours disponible
        if (!isBookAvailable($book_id)) {
            throw new Exception("Le livre n'est plus disponible");
        }
        
        // Mise à jour du statut du livre
        $stmt = $pdo->prepare("UPDATE books SET status = 'reserve' WHERE id = ?");
        $stmt->execute([$book_id]);
        
        // Création de la réservation
        $stmt = $pdo->prepare("INSERT INTO reservations (book_id, user_id) VALUES (?, ?)");
        $stmt->execute([$book_id, $user_id]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Fonction pour annuler une réservation
function cancelReservation($reservation_id, $user_id = null) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Si user_id est null (admin), on vérifie juste l'existence de la réservation
        if ($user_id === null) {
            $stmt = $pdo->prepare("SELECT book_id FROM reservations WHERE id = ?");
            $stmt->execute([$reservation_id]);
        } else {
            // Sinon on vérifie aussi que la réservation appartient à l'utilisateur
            $stmt = $pdo->prepare("SELECT book_id FROM reservations WHERE id = ? AND user_id = ?");
            $stmt->execute([$reservation_id, $user_id]);
        }
        $reservation = $stmt->fetch();
        
        if (!$reservation) {
            throw new Exception("Réservation non trouvée");
        }
        
        // Supprime la réservation
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$reservation_id]);
        
        // Mise à jour du statut du livre
        $stmt = $pdo->prepare("UPDATE books SET status = 'disponible' WHERE id = ?");
        $stmt->execute([$reservation['book_id']]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
