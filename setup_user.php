<?php
/**
 * Script de création d'un utilisateur de test
 * 
 * USAGE: php setup_user.php
 * SUPPRIMER CE FICHIER après utilisation.
 */

require_once __DIR__ . '/php/fonction.php';

// === CONFIGURATION — modifiez ces valeurs ===
$nom     = 'DOLSOM';
$prenom  = 'Bouda';
$login   = 'dolsom';
$password = 'dolsom123';   // Changez ce mot de passe !
$compte  = 'ADMIN';        // ADMIN = accès complet, CAISSE = caisse uniquement, STOCK = stock uniquement
// ============================================

// Vérifier si l'utilisateur existe déjà
$existing = SQLSelect("SELECT * FROM user WHERE loginUser = :login", [':login' => $login]);
if ($existing) {
    echo "L'utilisateur '{$login}' existe déjà. Mise à jour du mot de passe...\n";
    SQLExecute("UPDATE user SET mdpUser = :mdp WHERE loginUser = :login", [
        ':mdp'   => password_hash($password, PASSWORD_DEFAULT),
        ':login' => $login
    ]);
    echo "✓ Mot de passe mis à jour.\n";
} else {
    SQLExecute("INSERT INTO user (nomUser, prenomUser, loginUser, mdpUser, statutCompteUser, user_codeCompte) 
        VALUES (:nom, :prenom, :login, :mdp, :statut, :compte)", [
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':login'  => $login,
        ':mdp'    => password_hash($password, PASSWORD_DEFAULT),
        ':statut' => 1,
        ':compte' => $compte
    ]);
    echo "✓ Utilisateur créé avec succès!\n";
}

echo "\n  Login:    {$login}\n";
echo "  Password: {$password}\n";
echo "  Compte:   {$compte}\n";
echo "\n⚠️  SUPPRIMEZ ce fichier (setup_user.php) après utilisation!\n";
