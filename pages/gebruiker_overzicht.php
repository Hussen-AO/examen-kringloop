<?php
/*
Naam script     : gebruiker_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Overzicht gebruikers + verwijderen met bevestiging
Auteur          : hussen
*/

require_once "../config/auth_check.php";
requireDirectie();

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Gebruiker.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// gebruiker object
$gebruiker = new Gebruiker($conn);

$fout = "";
$goed = "";

// delete bevestiging (zelfde pagina)
$delete_id = $_GET["delete_id"] ?? "";

// als echt verwijderen is gedrukt
if (isset($_POST["verwijder"]) && isset($_POST["id"])) {

    $id = $_POST["id"];

    if ($id == "" || !is_numeric($id)) {
        $fout = "Ongeldig id.";
    } else {
        $gebruiker->verwijderen($id);
        $goed = "Gebruiker is verwijderd.";
    }
}

// overzicht ophalen
$gebruikers = $gebruiker->getOverzicht();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Gebruiker overzicht</title>
</head>
<body>

<h1>Gebruiker overzicht</h1>

<a href="gebruiker_form.php">+ Gebruiker toevoegen</a>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<?php if ($delete_id != "" && is_numeric($delete_id)) { ?>
    <form method="post">
        <p>Weet je zeker dat je gebruiker ID <?php echo htmlspecialchars($delete_id); ?> wilt verwijderen?</p>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($delete_id); ?>">
        <button type="submit" name="verwijder">Ja, verwijderen</button>
        <a href="gebruiker_overzicht.php">Annuleren</a>
    </form>
<?php } ?>

<table border="1">
<tr>
    <th>ID</th>
    <th>Gebruikersnaam</th>
    <th>Rollen</th>
    <th>Acties</th>
</tr>

<?php foreach ($gebruikers as $g) { ?>
<tr>
    <td><?php echo $g["id"]; ?></td>
    <td><?php echo htmlspecialchars($g["gebruikersnaam"]); ?></td>
    <td><?php echo htmlspecialchars($g["rollen"]); ?></td>
    <td>
        <a href="gebruiker_overzicht.php?delete_id=<?php echo $g["id"]; ?>">Verwijderen</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
