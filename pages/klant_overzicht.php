<?php
/*
Naam script     : klant_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Overzicht klanten + verwijderen met bevestiging
Auteur          : Sam
*/

require_once "../config/auth_check.php";
requireAnyRole(['winkelpersoneel']);

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Klant.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// klant object
$klant = new Klant($conn);

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
        $klant->verwijderen($id);
        $goed = "Klant is verwijderd.";
    }
}

// overzicht ophalen
$klanten = $klant->getOverzicht($zoek);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Klant overzicht</title>
</head>
<body>

<h1>Klant overzicht</h1>

<a href="klant_form.php">+ Klant toevoegen</a>

<form method="get">
    <input type="text" name="zoek" value="<?php echo htmlspecialchars($zoek); ?>" placeholder="Zoek klant">
    <button type="submit">Zoeken</button>
    <a href="klant_overzicht.php">Reset</a>
</form>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<?php if ($delete_id != "" && is_numeric($delete_id)) { ?>
    <form method="post">
        <p>Weet je zeker dat je klant ID <?php echo htmlspecialchars($delete_id); ?> wilt verwijderen?</p>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($delete_id); ?>">
        <button type="submit" name="verwijder">Ja, verwijderen</button>
        <a href="klant_overzicht.php">Annuleren</a>
    </form>
<?php } ?>

<table border="1">
<tr>
    <th>ID</th>
    <th>Naam</th>
    <th>Plaats</th>
    <th>Telefoon</th>
    <th>Email</th>
    <th>Acties</th>
</tr>

<?php foreach ($klanten as $k) { ?>
<tr>
    <td><?php echo $k["id"]; ?></td>
    <td><?php echo htmlspecialchars($k["naam"]); ?></td>
    <td><?php echo htmlspecialchars($k["plaats"]); ?></td>
    <td><?php echo htmlspecialchars($k["telefoon"]); ?></td>
    <td><?php echo htmlspecialchars($k["email"]); ?></td>
    <td>
        <a href="klant_form.php?id=<?php echo $k["id"]; ?>">Wijzigen</a> |
        <a href="klant_overzicht.php?delete_id=<?php echo $k["id"]; ?>">Verwijderen</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
