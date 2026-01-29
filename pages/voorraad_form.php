<?php
/*
Naam script     : voorraad_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Voorraad toevoegen of wijzigen (1 formulier)
Auteur          : 
*/

require_once "../config/auth_check.php";
requireAnyRole(['magazijnmedewerker ']);

require_once "../config/auth_check.php";
require_once "../config/Database.php";
require_once "../klasses/Voorraad.php";

// database verbinden
$db = new Database();
$conn = $db->connect();

// voorraad object
$voorraad = new Voorraad($conn);

$fout = "";
$goed = "";

// id ophalen (als er een id is, is het wijzigen)
$id = $_GET["id"] ?? "";

// dropdown data
$artikelen = $voorraad->getArtikelen();
$statussen = $voorraad->getStatussen();

// standaard waardes
$data = [
    "artikel_id" => "",
    "aantal" => "",
    "locatie" => "",
    "status_id" => ""
];

// als wijzigen, voorraad item ophalen
if ($id != "") {
    if (!is_numeric($id)) {
        die("Ongeldig id.");
    }

    $gevonden = $voorraad->getById($id);
    if (!$gevonden) {
        die("Voorraad item niet gevonden.");
    }

    $data = $gevonden;
}

// formulier verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $artikel_id = $_POST["artikel_id"] ?? "";
    $aantal = trim($_POST["aantal"] ?? "");
    $locatie = trim($_POST["locatie"] ?? "");
    $status_id = $_POST["status_id"] ?? "";

    // bij wijzigen mag artikel_id niet leeg zijn, maar we wijzigen hem niet
    if ($id != "" && $data["artikel_id"] != "") {
        $artikel_id = $data["artikel_id"];
    }

    // simpele validatie
    if ($artikel_id == "" || $aantal == "" || $locatie == "" || $status_id == "") {
        $fout = "Vul alle velden in.";
    } elseif (!is_numeric($aantal) || $aantal < 0) {
        $fout = "Aantal moet een nummer zijn.";
    } elseif (strlen($locatie) < 1 || strlen($locatie) > 30) {
        $fout = "Locatie is niet geldig.";
    } else {

        if ($id == "") {
            $voorraad->toevoegen($artikel_id, $aantal, $locatie, $status_id);
            $goed = "Voorraad is toegevoegd.";

            // velden leegmaken
            $data = [
                "artikel_id" => "",
                "aantal" => "",
                "locatie" => "",
                "status_id" => ""
            ];
        } else {
            $voorraad->wijzigen($id, $aantal, $locatie, $status_id);
            $goed = "Voorraad is aangepast.";

            $data = $voorraad->getById($id);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Voorraad formulier</title>
</head>
<body>

<h1><?php echo ($id == "" ? "Voorraad toevoegen" : "Voorraad wijzigen"); ?></h1>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<form method="post">

<label>Artikel</label><br>
<select name="artikel_id" <?php echo ($id != "" ? "disabled" : "required"); ?>>
    <option value="">-- kies artikel --</option>
    <?php foreach ($artikelen as $a) { ?>
        <option value="<?php echo $a["id"]; ?>"
            <?php echo ($a["id"] == $data["artikel_id"] ? "selected" : ""); ?>>
            <?php echo htmlspecialchars($a["naam"]); ?>
        </option>
    <?php } ?>
</select>

<?php if ($id != "") { ?>
    <!-- artikel id meegeven bij wijzigen -->
    <input type="hidden" name="artikel_id" value="<?php echo htmlspecialchars($data["artikel_id"]); ?>">
<?php } ?>

<br><br>

<label>Aantal</label><br>
<input type="number" name="aantal" value="<?php echo htmlspecialchars($data["aantal"]); ?>" required>
<br><br>

<label>Locatie</label><br>
<input type="text" name="locatie" value="<?php echo htmlspecialchars($data["locatie"]); ?>" required>
<br><br>

<label>Status</label><br>
<select name="status_id" required>
    <option value="">-- kies status --</option>
    <?php foreach ($statussen as $s) { ?>
        <option value="<?php echo $s["id"]; ?>"
            <?php echo ($s["id"] == $data["status_id"] ? "selected" : ""); ?>>
            <?php echo htmlspecialchars($s["status"]); ?>
        </option>
    <?php } ?>
</select>

<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="voorraad_overzicht.php">Terug</a>

</body>
</html>
