<?php
/*
Naam script     : logout.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Uitloggen en sessie verwijderen
Auteur          : 
*/

session_start();
session_destroy();

header("Location: login.php");
exit;
?>
