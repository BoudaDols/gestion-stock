<?php
/**
 * Script de migration des mots de passe MD5 vers password_hash (bcrypt)
 * 
 * USAGE: Exécuter UNE SEULE FOIS après la mise à jour du code.
 * Ce script réinitialise le mot de passe de tous les utilisateurs
 * à leur login (comme le fait la fonction "Réinitialiser" dans majuser.php).
 * 
 * Après exécution, chaque utilisateur devra changer son mot de passe
 * via "Mon Compte" en utilisant son login comme mot de passe temporaire.
 * 
 * SUPPRIMER CE FICHIER après utilisation.
 */

require_once __DIR__ . '/php/fonction.php';

echo "=== Migration des mots de passe MD5 → bcrypt ===\n\n";

$users = SQLSelect("SELECT idUser, loginUser, nomUser, prenomUser FROM user");

if (!$users) {
    echo "Aucun utilisateur trouvé.\n";
    exit;
}

$count = 0;
foreach ($users as $user) {
    // Le mot de passe par défaut est le login de l'utilisateur
    $newHash = password_hash($user->loginUser, PASSWORD_DEFAULT);
    
    SQLExecute("UPDATE user SET mdpUser = :mdp WHERE idUser = :id", [
        ':mdp' => $newHash,
        ':id'  => $user->idUser
    ]);
    
    echo "✓ {$user->nomUser} {$user->prenomUser} ({$user->loginUser}) — mot de passe réinitialisé à son login\n";
    $count++;
}

echo "\n=== {$count} utilisateur(s) migré(s) ===\n";
echo "Chaque utilisateur peut maintenant se connecter avec son login comme mot de passe.\n";
echo "Ils devront changer leur mot de passe via 'Mon Compte'.\n\n";
echo "⚠️  SUPPRIMEZ CE FICHIER (migration_passwords.php) maintenant !\n";
