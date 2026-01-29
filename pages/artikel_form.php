<?php
/*
Naam script     : artikel_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Artikel toevoegen of wijzigen (1 formulier)
Auteur          : Sam
*/

//hier worden de volgende file meegenomen in de form:
require_once "../config/auth_check.php";
//
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

// id ophalen (als er een id is, is het wijzigen)
$id = $_GET["id"] ?? "";

// standaard waardes voor formulier
$data = [
    "categorie_id" => "",
    "naam" => "",
    "prijs_ex_btw" => ""
];

// als wijzigen, artikel ophalen
if ($id != "") {
    if (!is_numeric($id)) {
        die("Ongeldig id.");
    }

    $gevonden = $artikel->getById($id);
    if (!$gevonden) {
        die("Artikel niet gevonden.");
    }

    $data = $gevonden;
}

// dropdown categorieën
$categorieen = $artikel->getCategorieen();

// formulier verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $categorie_id = $_POST["categorie_id"] ?? "";
    $naam = trim($_POST["naam"] ?? "");
    $prijs = trim($_POST["prijs"] ?? "");

    // simpele validatie
    if ($categorie_id == "" || $naam == "" || $prijs == "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($naam) < 2 || strlen($naam) > 50) {
        $fout = "Naam is niet geldig.";
    } elseif (!is_numeric($prijs)) {
        $fout = "Prijs moet een nummer zijn.";
    } else {

        // als id leeg is: toevoegen, anders wijzigen
        if ($id == "") {
            $artikel->toevoegen($categorie_id, $naam, $prijs);
            $goed = "Artikel is toegevoegd.";

            // velden leegmaken na toevoegen
            $data["categorie_id"] = "";
            $data["naam"] = "";
            $data["prijs_ex_btw"] = "";

        } else {
            $artikel->wijzigen($id, $categorie_id, $naam, $prijs);
            $goed = "Artikel is aangepast.";

            // opnieuw ophalen
            $data = $artikel->getById($id);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Artikel formulier</title>
</head>
<body>

<h1><?php echo ($id == "" ? "Artikel toevoegen" : "Artikel wijzigen"); ?></h1>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<form method="post">

<label>Categorie</label><br>
<select name="categorie_id" required>
    <option value="">-- kies categorie --</option>
    <?php foreach ($categorieen as $c) { ?>
        <option value="<?php echo $c["id"]; ?>" <?php if ($data["categorie_id"] == $c["id"]) echo "selected"; ?>>
            <?php echo htmlspecialchars($c["categorie"]); ?>
        </option>
    <?php } ?>
</select>
<br><br>

<label>Naam</label><br>
<input type="text" name="naam" maxlength="50" value="<?php echo htmlspecialchars($data["naam"]); ?>" required>
<br><br>

<label>Prijs (ex btw)</label><br>
<input type="text" name="prijs" value="<?php echo htmlspecialchars($data["prijs_ex_btw"]); ?>" required>
<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="artikel_overzicht.php">Terug</a>

</body>
</html>
