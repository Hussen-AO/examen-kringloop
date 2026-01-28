<?php
/*
Naam script     : planning_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Ritplanning toevoegen (1 formulier)
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

$fout = "";
$goed = "";

// dropdown data
$klanten = $planning->getKlanten();
$artikelen = $planning->getArtikelen();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $klant_id = $_POST["klant_id"] ?? "";
    $artikel_id = $_POST["artikel_id"] ?? "";
    $kenteken = strtoupper(trim($_POST["kenteken"] ?? ""));
    $type = $_POST["type"] ?? "";
    $afspraak_op = $_POST["afspraak_op"] ?? "";

    // simpele validatie
    if ($klant_id == "" || $artikel_id == "" || $kenteken == "" || $type == "" || $afspraak_op == "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($kenteken) < 4 || strlen($kenteken) > 12) {
        $fout = "Kenteken is niet geldig.";
    } else {
        $planning->toevoegen($klant_id, $artikel_id, $kenteken, $type, $afspraak_op);
        $goed = "Rit is toegevoegd.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Planning formulier</title>
</head>
<body>

<h1>Rit toevoegen</h1>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<form method="post">

<label>Klant</label><br>
<select name="klant_id" required>
    <option value="">-- kies klant --</option>
    <?php foreach ($klanten as $k) { ?>
        <option value="<?php echo $k["id"]; ?>"><?php echo htmlspecialchars($k["naam"]); ?></option>
    <?php } ?>
</select>
<br><br>

<label>Artikel</label><br>
<select name="artikel_id" required>
    <option value="">-- kies artikel --</option>
    <?php foreach ($artikelen as $a) { ?>
        <option value="<?php echo $a["id"]; ?>"><?php echo htmlspecialchars($a["naam"]); ?></option>
    <?php } ?>
</select>
<br><br>

<label>Kenteken</label><br>
<input type="text" name="kenteken" maxlength="12" required>
<br><br>

<label>Type</label><br>
<select name="type" required>
    <option value="">-- kies --</option>
    <option value="ophalen">Ophalen</option>
    <option value="bezorgen">Bezorgen</option>
</select>
<br><br>

<label>Afspraak datum en tijd</label><br>
<input type="datetime-local" name="afspraak_op" required>
<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="planning_overzicht.php">Terug</a>

</body>
</html>
