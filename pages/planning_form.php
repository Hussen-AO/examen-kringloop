<?php
/*
Naam script     : planning_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Ritplanning toevoegen (1 formulier)
Auteur          : jayjay stam
*/

// Controleer of gebruiker ingelogd is en het juiste rol heeft
require_once "../config/auth_check.php";
requireAnyRole(['chauffeur']);

// Laad benodigde classes en configuratie
require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Planning.php";

// Initialiseer database verbinding
$db = new Database();
$conn = $db->connect();

// Maak een nieuw Planning object voor database operaties
$planning = new Planning($conn);

// Variabelen voor feedback aan gebruiker
$fout = "";
$goed = "";

// Laad alle klanten en artikelen voor de dropdown menu's
$klanten = $planning->getKlanten();
$artikelen = $planning->getArtikelen();

// Verwerk het formulier als het ingediend is
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Haal formuliergegevens op en zet ze in variabelen
    $klant_id = $_POST["klant_id"] ?? "";
    $artikel_id = $_POST["artikel_id"] ?? "";
    $kenteken = strtoupper(trim($_POST["kenteken"] ?? ""));
    $type = $_POST["type"] ?? "";
    $afspraak_op = $_POST["afspraak_op"] ?? "";

    // Valideer dat alle verplichte velden ingevuld zijn
    if ($klant_id == "" || $artikel_id == "" || $kenteken == "" || $type == "" || $afspraak_op == "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($kenteken) < 4 || strlen($kenteken) > 12) {
        // Controleer of het kenteken het juiste formaat heeft
        $fout = "Kenteken is niet geldig.";
    } else {
        // Als alles klopt, voeg de nieuwe rit toe aan de database
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
