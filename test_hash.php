<?php
$password = "user123";
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Nouveau hash pour 'user123': " . $hash . "\n";
echo "Test de vérification: " . (password_verify($password, $hash) ? "OK" : "ÉCHEC") . "\n";

// Test avec le hash existant
$stored_hash = '$2y$10$pKhkZViXR9YgqQX7VeKqn.wGj3qQKs7yCT1wVBQM3UYY7agoLFCye';
echo "Test avec le hash stocké: " . (password_verify($password, $stored_hash) ? "OK" : "ÉCHEC") . "\n";
?>
