<?php
// Test complet de hachage et vérification
function testPasswordHash($username, $password) {
    // Créer un nouveau hash
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Test pour $username:\n";
    echo "Mot de passe: $password\n";
    echo "Nouveau hash: $new_hash\n";
    echo "Vérification immédiate: " . (password_verify($password, $new_hash) ? "OK" : "ÉCHEC") . "\n\n";
    return $new_hash;
}

// Tester admin
$admin_hash = testPasswordHash('admin', 'admin123');

// Tester user
$user_hash = testPasswordHash('user', 'user123');

// Générer le SQL de mise à jour
echo "SQL pour mettre à jour la base de données:\n";
echo "UPDATE users SET password = '$admin_hash' WHERE username = 'admin';\n";
echo "UPDATE users SET password = '$user_hash' WHERE username = 'user';\n";
?>
