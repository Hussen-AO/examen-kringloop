<?php
/*
Naam script     : klant_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Klant toevoegen of wijzigen (1 formulier)
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

// id ophalen (als er een id is, is het wijzigen)
$id = $_GET["id"] ?? "";

// standaard waardes
$data = [
    "naam" => "",
    "adres" => "",
    "plaats" => "",
    "telefoon" => "",
    "email" => ""
];

// als wijzigen, klant ophalen
if ($id != "") {
    if (!is_numeric($id)) {
        die("Ongeldig id.");
    }

    $gevonden = $klant->getById($id);
    if (!$gevonden) {
        die("Klant niet gevonden.");
    }

    $data = $gevonden;
}

// formulier verwerken
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $naam = trim($_POST["naam"] ?? "");
    $adres = trim($_POST["adres"] ?? "");
    $plaats = trim($_POST["plaats"] ?? "");
    $telefoon = trim($_POST["telefoon"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // simpele validatie
    if ($naam == "" || $adres == "" || $plaats == "" || $telefoon == "" || $email == "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($naam) < 2 || strlen($naam) > 60) {
        $fout = "Naam is niet geldig.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fout = "Email is niet geldig.";
    } else {

        // toevoegen of wijzigen
        if ($id == "") {
            $klant->toevoegen($naam, $adres, $plaats, $telefoon, $email);
            $goed = "Klant is toegevoegd.";

            // velden leegmaken
            $data = [
                "naam" => "",
                "adres" => "",
                "plaats" => "",
                "telefoon" => "",
                "email" => ""
            ];
        } else {
            $klant->wijzigen($id, $naam, $adres, $plaats, $telefoon, $email);
            $goed = "Klant is aangepast.";

            // opnieuw ophalen
            $data = $klant->getById($id);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Klant formulier</title>
</head>
<body>

<h1><?php echo ($id == "" ? "Klant toevoegen" : "Klant wijzigen"); ?></h1>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<form method="post">

<label>Naam</label><br>
<input type="text" name="naam" value="<?php echo htmlspecialchars($data["naam"]); ?>" required>
<br><br>

<label>Adres</label><br>
<input type="text" name="adres" value="<?php echo htmlspecialchars($data["adres"]); ?>" required>
<br><br>

<label>Plaats</label><br>
<input type="text" name="plaats" value="<?php echo htmlspecialchars($data["plaats"]); ?>" required>
<br><br>

<label>Telefoon</label><br>
<input type="text" name="telefoon" value="<?php echo htmlspecialchars($data["telefoon"]); ?>" required>
<br><br>

<label>Email</label><br>
<input type="email" name="email" value="<?php echo htmlspecialchars($data["email"]); ?>" required>
<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="klant_overzicht.php">Terug</a>

</body>
</html>
