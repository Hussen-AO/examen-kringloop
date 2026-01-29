<?php
/*
Naam script     : voorraad_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Voorraad toevoegen of wijzigen (1 formulier)
Auteur          : jayjay stamm
*/

// Controleer of gebruiker ingelogd is en het juiste rol heeft
require_once "../config/auth_check.php";
requireAnyRole(['magazijnmedewerker ']);

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

// Haal het ID op uit de URL - als er een ID is, gaat het om wijzigen in plaats van toevoegen
$id = $_GET["id"] ?? "";

// Laad alle artikelen en statussen voor de dropdown menu's
$artikelen = $voorraad->getArtikelen();
$statussen = $voorraad->getStatussen();

// Stel standaard waarden in voor het formulier
$data = [
    "artikel_id" => "",
    "aantal" => "",
    "locatie" => "",
    "status_id" => ""
];

// Als er een ID is gegeven, laad het bestaande voorraad item
if ($id != "") {
    // Valideer dat het ID een nummer is
    if (!is_numeric($id)) {
        die("Ongeldig id.");
    }

    // Zoek het voorraad item in de database
    $gevonden = $voorraad->getById($id);
    if (!$gevonden) {
        die("Voorraad item niet gevonden.");
    }

    // Vul de gegevens met bestaande waarden
    $data = $gevonden;
}

// Verwerk het formulier als het ingediend is
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Haal formuliergegevens op
    $artikel_id = $_POST["artikel_id"] ?? "";
    $aantal = trim($_POST["aantal"] ?? "");
    $locatie = trim($_POST["locatie"] ?? "");
    $status_id = $_POST["status_id"] ?? "";

    // Bij wijzigen mag artikel_id niet leeg zijn, maar we wijzigen hem niet
    if ($id != "" && $data["artikel_id"] != "") {
        $artikel_id = $data["artikel_id"];
    }

    // Valideer dat alle verplichte velden ingevuld zijn
    if ($artikel_id == "" || $aantal == "" || $locatie == "" || $status_id == "") {
        $fout = "Vul alle velden in.";
    } elseif (!is_numeric($aantal) || $aantal < 0) {
        // Controleer of het aantal een geldig positief getal is
        $fout = "Aantal moet een nummer zijn.";
    } elseif (strlen($locatie) < 1 || strlen($locatie) > 30) {
        // Controleer of de locatie het juiste lengte heeft
        $fout = "Locatie is niet geldig.";
    } else {
        // Controleer of we toevoegen of wijzigen
        if ($id == "") {
            // Voeg nieuwe voorraad toe
            $voorraad->toevoegen($artikel_id, $aantal, $locatie, $status_id);
            $goed = "Voorraad is toegevoegd.";

            // Leegmaken van formuliervelden na succesvol toevoegen
            $data = [
                "artikel_id" => "",
                "aantal" => "",
                "locatie" => "",
                "status_id" => ""
            ];
        } else {
            // Wijzig bestaande voorraad
            $voorraad->wijzigen($id, $aantal, $locatie, $status_id);
            $goed = "Voorraad is aangepast.";

            // Herlaad de gegevens om zeker te zijn van de update
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
