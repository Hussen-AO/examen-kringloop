<?php
/*
Naam script     : auth_check.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : login + rol checks (directie mag overal)
Auteur          :hussen
*/

// Start sessie als deze nog niet actief is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// checkt of variabele session userid leeg waar wordt je naar login verwijst
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: /pages/login.php");
        exit;
    }
}

// Controleert of gebruiker ingelogd is en de juiste rol heeft
// Directie gebruikers hebben toegang tot alles
// Andere gebruikers moeten in de toegestane rollen staan
function requireAnyRole(array $rolesAllowed): void {
    requireLogin();

    // Haal rol op uit sessie (default lege string als niet gezet)
    $role = $_SESSION['role'] ?? '';

    // Directie heeft altijd toegang
    if ($role === 'directie') {
        return;
    }

    // Controleer of rol in toegestane rollen staat
    if (!in_array($role, $rolesAllowed, true)) {
        header("Location: ../index.php");
        exit;
    }
}

// Controleert of gebruiker de rol 'directie' heeft
function requireDirectie(): void {
    requireAnyRole(['directie']);
}
