<?php
/*
Naam script     : planning_overzicht.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Overzicht van ritplanning
Auteur          : 
*/

require_once "../config/auth_check.php";
requireAnyRole(['chauffeur']);

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Planning.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// planning object
$planning = new Planning($conn);

// filter type ophalen
$type = $_GET["type"] ?? "";

// overzicht ophalen
$ritten = $planning->getOverzicht($type);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ritplanning overzicht</title>
</head>
<body>

<h1>Ritplanning overzicht</h1>

<a href="planning_form.php">+ Rit toevoegen</a>

<form method="get">
    <label>Filter</label>
    <select name="type">
        <option value="">Alles</option>
        <option value="ophalen" <?php if ($type == "ophalen") echo "selected"; ?>>Ophalen</option>
        <option value="bezorgen" <?php if ($type == "bezorgen") echo "selected"; ?>>Bezorgen</option>
    </select>
    <button type="submit">Filter</button>
    <a href="planning_overzicht.php">Reset</a>
</form>

<table border="1">
<tr>
    <th>ID</th>
    <th>Klant</th>
    <th>Artikel</th>
    <th>Kenteken</th>
    <th>Type</th>
    <th>Afspraak</th>
</tr>

<?php if (count($ritten) == 0) { ?>
<tr><td colspan="6">Geen ritten gevonden.</td></tr>
<?php } ?>

<?php foreach ($ritten as $r) { ?>
<tr>
    <td><?php echo $r["id"]; ?></td>
    <td><?php echo htmlspecialchars($r["klant"]); ?></td>
    <td><?php echo htmlspecialchars($r["artikel"]); ?></td>
    <td><?php echo htmlspecialchars($r["kenteken"]); ?></td>
    <td><?php echo htmlspecialchars($r["ophalen_of_bezorgen"]); ?></td>
    <td><?php echo htmlspecialchars($r["afspraak_op"]); ?></td>
</tr>
<?php } ?>

</table>

<a href="dashboard.php">Terug</a>

</body>
</html>
