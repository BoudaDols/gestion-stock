<?php
/**
 * Gestion sécurisée des sessions
 * Inclure ce fichier au lieu d'appeler session_start() directement
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
    ]);
}

// Générer un token CSRF s'il n'existe pas encore
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
