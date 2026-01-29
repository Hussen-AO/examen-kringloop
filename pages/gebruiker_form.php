<?php
/*
Naam script     : gebruiker_form.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : Gebruiker toevoegen (1 formulier)
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

// standaard waardes
$data = [
    "gebruikersnaam" => "",
    "rollen" => ""
];

// formulier verwerken 
//REQUEST_METHOD controllert of formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $gebruikersnaam = trim($_POST["gebruikersnaam"] ?? "");
    $wachtwoord = $_POST["wachtwoord"] ?? "";
    $rollen = trim($_POST["rollen"] ?? "");

    // simpele validatie
    if ($gebruikersnaam == "" || $wachtwoord == "" || $rollen == "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($gebruikersnaam) < 3 || strlen($gebruikersnaam) > 30) {
        $fout = "Gebruikersnaam is niet geldig.";
    } elseif (strlen($wachtwoord) < 4) {
        $fout = "Wachtwoord is te kort.";
    } elseif ($gebruiker->bestaatGebruikersnaam($gebruikersnaam)) {
        $fout = "Gebruikersnaam bestaat al.";
    } else {
        $gebruiker->toevoegen($gebruikersnaam, $wachtwoord, $rollen);
        $goed = "Gebruiker is toegevoegd.";

        // leegmaken
        $data["gebruikersnaam"] = "";
        $data["rollen"] = "";
    }

    // ingevulde velden terugzetten als er fout is
    if ($fout != "") {
        $data["gebruikersnaam"] = $gebruikersnaam;
        $data["rollen"] = $rollen;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Gebruiker toevoegen</title>
</head>
<body>

<h1>Gebruiker toevoegen</h1>

<?php if ($fout != "") echo "<p>$fout</p>"; ?>
<?php if ($goed != "") echo "<p>$goed</p>"; ?>

<form method="post">

<label>Gebruikersnaam</label><br>
<input type="text" name="gebruikersnaam" value="<?php echo htmlspecialchars($data["gebruikersnaam"]); ?>" required>
<br><br>

<label>Wachtwoord</label><br>
<input type="password" name="wachtwoord" required>
<br><br>

<label>Rollen</label><br>
<input type="text" name="rollen" value="<?php echo htmlspecialchars($data["rollen"]); ?>" placeholder="bijv: directie" required>
<br><br>

<button type="submit">Opslaan</button>

</form>

<a href="gebruiker_overzicht.php">Terug</a>

</body>
</html>
