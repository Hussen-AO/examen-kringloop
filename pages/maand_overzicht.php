<?php
/*
Naam script     : maand_overzicht.php
Versie          : 1.0
Datum           : 29-01-2026
Beschrijving    : Toont de totale omzet per maand
Auteur          : 
*/

// Controleer of gebruiker ingelogd is en heeft juiste rol
require_once "../config/auth_check.php";
requireDirectie();

// Database en klasse laden
require_once "../config/Database.php";
require_once "../klasses/Verkoop.php";

// Database verbinding maken
$db = new Database();
$conn = $db->connect();

// Verkoop object aanmaken
$verkoop = new Verkoop($conn);

// Jaar en maand ophalen uit GET parameters, standaard huidige maand/jaar
$jaar = (int)($_GET["jaar"] ?? date("Y"));
$maand = (int)($_GET["maand"] ?? date("n"));

// Validatie: zorg dat maand tussen 1 en 12 is
if ($maand < 1 || $maand > 12) {
    $maand = (int)date("n");
}

// Haal omzet op voor geselecteerde maand en jaar
$omzet = $verkoop->getOmzetMaand($jaar, $maand);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Maand omzet</title>
</head>
<body>

<h1>Maand omzet</h1>

<form method="get">
    <label>Maand</label>
    <select name="maand">
        <?php for ($m = 1; $m <= 12; $m++) { ?>
            <option value="<?php echo $m; ?>" <?php if ($m === $maand) echo "selected"; ?>>
                <?php echo $m; ?>
            </option>
        <?php } ?>
    </select>

    <label>Jaar</label>
    <input type="number" name="jaar" value="<?php echo $jaar; ?>" min="2000" max="2100">

    <button type="submit">Toon</button>
</form>

<br>

<p>
    Totale omzet (ex btw):
    <strong>€ <?php echo number_format((float)$omzet, 2, ",", "."); ?></strong>
</p>

</body>
</html>
