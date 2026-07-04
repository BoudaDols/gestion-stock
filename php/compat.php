<?php
/**
 * Polyfills pour la compatibilité PHP 8.2+
 * 
 * utf8_encode() et utf8_decode() ont été supprimées en PHP 8.2.
 * Ce fichier fournit des équivalents pour les bibliothèques tierces (fpdf, html2pdf)
 * qui les utilisent encore.
 */

if (!function_exists('utf8_encode')) {
    function utf8_encode(string $string): string
    {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }
}

if (!function_exists('utf8_decode')) {
    function utf8_decode(string $string): string
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
    }
}
