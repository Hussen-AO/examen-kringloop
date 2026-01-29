<?php
/*
Naam script     : verkoop_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Verkoop overzicht pagina
Auteur          : hussen
*/

require_once "../config/auth_check.php";
requireAnyRole(['winkelpersoneel']);

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Verkoop.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// verkoop object
$verkoop = new Verkoop($conn);

// filter waarden
$van = $_GET["van"] ?? "";
$tot = $_GET["tot"] ?? "";

// overzicht ophalen
$verkopen = $verkoop->getOverzicht($van, $tot);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Verkoop overzicht</title>
</head>
<body>

<h1>Verkoop overzicht</h1>

<a href="verkoop_form.php">+ Verkoop toevoegen</a>

<form method="get">
    <label>Van</label>
    <input type="date" name="van" value="<?php echo htmlspecialchars($van); ?>">

    <label>Tot</label>
    <input type="date" name="tot" value="<?php echo htmlspecialchars($tot); ?>">

    <button type="submit">Filter</button>
    <a href="verkoop_overzicht.php">Reset</a>
</form>

<table border="1">
<tr>
    <th>ID</th>
    <th>Klant</th>
    <th>Artikel</th>
    <th>Prijs</th>
    <th>Verkocht op</th>
</tr>

<?php if (count($verkopen) == 0) { ?>
<tr><td colspan="5">Geen verkopen gevonden.</td></tr>
<?php } ?>

<?php foreach ($verkopen as $v) { ?>
<tr>
    <td><?php echo $v["id"]; ?></td>
    <td><?php echo htmlspecialchars($v["klant"]); ?></td>
    <td><?php echo htmlspecialchars($v["artikel"]); ?></td>
    <td><?php echo htmlspecialchars($v["prijs_ex_btw"]); ?></td>
    <td><?php echo htmlspecialchars($v["verkocht_op"]); ?></td>
</tr>
<?php } ?>

</table>

<a href="../index.php">Terug</a>

</body>
</html>
