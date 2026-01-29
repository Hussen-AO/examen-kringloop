<?php
/*
Naam script     : artikel_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Overzicht artikelen + verwijderen met bevestiging
Auteur          : Sam
*/

require_once "../config/auth_check.php";
requireAnyRole(['winkelpersoneel']);

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Artikel.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// artikel object
$artikel = new Artikel($conn);

$fout = "";
$goed = "";

// zoeken
$zoek = trim($_GET["zoek"] ?? "");

// delete bevestiging (zelfde pagina)
$delete_id = $_GET["delete_id"] ?? "";

// als echt verwijderen is gedrukt
if (isset($_POST["verwijder"]) && isset($_POST["id"])) {

    $id = $_POST["id"];

    if ($id == "" || !is_numeric($id)) {
        $fout = "Ongeldig id.";
    } else {
        $artikel->verwijderen($id);
        $goed = "Artikel is verwijderd.";
    }
}

// overzicht ophalen
$artikelen = $artikel->getOverzicht($zoek);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Artikel overzicht</title>
</head>
<body>

<h1>Artikel overzicht</h1>

<a href="artikel_form.php">+ Artikel toevoegen</a>

<form method="get">
    <input type="text" name="zoek" value="<?php echo htmlspecialchars($zoek); ?>" placeholder="Zoek artikel">
    <button type="submit">Zoeken</button>
    <a href="artikel_overzicht.php">Reset</a>
</form>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<?php if ($delete_id != "" && is_numeric($delete_id)) { ?>
    <p>Weet je zeker dat je dit artikel wilt verwijderen?</p>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $delete_id; ?>">
        <button type="submit" name="verwijder">Ja</button>
        <a href="artikel_overzicht.php">Nee</a>
    </form>
    <hr>
<?php } ?>

<table border="1">
<tr>
    <th>ID</th>
    <th>Naam</th>
    <th>Categorie</th>
    <th>Prijs</th>
    <th>Acties</th>
</tr>

<?php if (count($artikelen) == 0) { ?>
<tr><td colspan="5">Geen artikelen gevonden.</td></tr>
<?php } ?>

<?php foreach ($artikelen as $a) { ?>
<tr>
    <td><?php echo $a["id"]; ?></td>
    <td><?php echo htmlspecialchars($a["naam"]); ?></td>
    <td><?php echo htmlspecialchars($a["categorie"]); ?></td>
    <td><?php echo htmlspecialchars($a["prijs_ex_btw"]); ?></td>
    <td>
        <a href="artikel_form.php?id=<?php echo $a["id"]; ?>">Wijzigen</a> |
        <a href="artikel_overzicht.php?delete_id=<?php echo $a["id"]; ?>">Verwijderen</a>
    </td>
</tr>
<?php } ?>

</table>

<a href="dashboard.php">Terug</a>

</body>
</html>
