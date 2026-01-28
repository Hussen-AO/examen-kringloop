<?php
/*
Naam script     : auth_check.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : login + rol checks (directie mag overal)
Auteur          :hussen
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: /pages/login.php");
        exit;
    }
}

function requireAnyRole(array $rolesAllowed): void {
    requireLogin();

    $role = $_SESSION['role'] ?? '';

    if ($role === 'directie') {
        return;
    }

    if (!in_array($role, $rolesAllowed, true)) {
        header("Location: ../pages/dashboard.php");
        exit;
    }
}

function requireDirectie(): void {
    requireAnyRole(['directie']);
}
