<?php
/*
Naam script     : verkoop_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Verkoop toevoegen (1 formulier)
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

$fout = "";
$goed = "";

// dropdown data
$klanten = $verkoop->getKlanten();
$artikelen = $verkoop->getArtikelen();

// formulier verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $klant_id = $_POST["klant_id"] ?? "";
    $artikel_id = $_POST["artikel_id"] ?? "";

    if ($klant_id == "" || $artikel_id == "") {
        $fout = "Kies een klant en een artikel.";
    } else {
        $verkoop->toevoegen($klant_id, $artikel_id);
        $goed = "Verkoop is toegevoegd.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Verkoop formulier</title>
</head>
<body>

<h1>Verkoop toevoegen</h1>

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
        <option value="<?php echo $a["id"]; ?>">
            <?php echo htmlspecialchars($a["naam"]); ?> (<?php echo $a["prijs_ex_btw"]; ?>)
        </option>
    <?php } ?>
</select>
<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="verkoop_overzicht.php">Terug</a>

</body>
</html>
