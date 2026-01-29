<?php
/*
Naam script     : voorraad_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Overzicht voorraad + verwijderen met bevestiging
Auteur          : jayjay stam
*/

// Controleer of gebruiker ingelogd is en het juiste rol heeft
require_once "../config/auth_check.php";
requireAnyRole(['magazijnmedewerker , winkelpersoneel']);

// Laad benodigde classes en configuratie
require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Voorraad.php";

// Initialiseer database verbinding
$db = new Database();
$conn = $db->connect();

// Maak een nieuw Voorraad object voor database operaties
$voorraad = new Voorraad($conn);

// Variabelen voor feedback aan gebruiker
$fout = "";
$goed = "";

// Haal de zoekterm op uit de URL parameters
$zoek = trim($_GET["zoek"] ?? "");

// Haal het delete ID op - gebruikt voor bevestigingsdialoog
$delete_id = $_GET["delete_id"] ?? "";

// Verwerk de delete request als het formulier is ingediend
if (isset($_POST["verwijder"]) && isset($_POST["id"])) {
    // Haal het ID op en valideer het
    $id = $_POST["id"];

    if ($id == "" || !is_numeric($id)) {
        $fout = "Ongeldig id.";
    } else {
        // Verwijder het voorraad item
        $voorraad->verwijderen($id);
        $goed = "Voorraad item is verwijderd.";
    }
}

// Haal het voorraad overzicht op, optioneel gefilterd op zoekterm
$items = $voorraad->getOverzicht($zoek);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Voorraad overzicht</title>
</head>
<body>

<h1>Voorraad overzicht</h1>

<a href="voorraad_form.php">+ Voorraad toevoegen</a>

<form method="get">
    <input type="text" name="zoek" value="<?php echo htmlspecialchars($zoek); ?>" placeholder="Zoek artikel id/naam">
    <button type="submit">Zoeken</button>
    <a href="voorraad_overzicht.php">Reset</a>
</form>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<?php if ($delete_id != "" && is_numeric($delete_id)) { ?>
    <form method="post">
        <p>Weet je zeker dat je voorraad ID <?php echo htmlspecialchars($delete_id); ?> wilt verwijderen?</p>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($delete_id); ?>">
        <button type="submit" name="verwijder">Ja, verwijderen</button>
        <a href="voorraad_overzicht.php">Annuleren</a>
    </form>
<?php } ?>

<table border="1">
<tr>
    <th>ID</th>
    <th>Artikel</th>
    <th>Aantal</th>
    <th>Locatie</th>
    <th>Status</th>
    <th>Ingeboekt op</th>
    <th>Acties</th>
</tr>

<?php foreach ($items as $v) { ?>
<tr>
    <td><?php echo $v["id"]; ?></td>
    <td><?php echo htmlspecialchars($v["naam"]); ?></td>
    <td><?php echo htmlspecialchars($v["aantal"]); ?></td>
    <td><?php echo htmlspecialchars($v["locatie"]); ?></td>
    <td><?php echo htmlspecialchars($v["status"]); ?></td>
    <td><?php echo htmlspecialchars($v["ingeboekt_op"]); ?></td>
    <td>
        <a href="voorraad_form.php?id=<?php echo $v["id"]; ?>">Wijzigen</a> |
        <a href="voorraad_overzicht.php?delete_id=<?php echo $v["id"]; ?>">Verwijderen</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
