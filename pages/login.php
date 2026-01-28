<?php
/*
Naam script     : login.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Inlog pagina met validatie
Auteur          : 
*/

session_start();
require_once "../config/Database.php";
require_once "../klasses/User.php";

$fout = "";

if (isset($_SESSION["user_id"])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role']; // moet exact één van die 4 strings zijn
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $gebruikersnaam = trim($_POST["gebruikersnaam"] ?? "");
    $wachtwoord = $_POST["wachtwoord"] ?? "";

    if ($gebruikersnaam === "" || $wachtwoord === "") {
        $fout = "Vul alle velden in.";
    } elseif (strlen($gebruikersnaam) < 3 || strlen($gebruikersnaam) > 25) {
        $fout = "Gebruikersnaam moet tussen 3 en 25 tekens zijn.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $gebruikersnaam)) {
        $fout = "Gebruikersnaam mag alleen letters, cijfers en _ bevatten.";
    } elseif (strlen($wachtwoord) < 6 || strlen($wachtwoord) > 50) {
        $fout = "Wachtwoord moet tussen 6 en 50 tekens zijn.";
    } else {

        $db = new Database();
        $conn = $db->connect();

        $userClass = new User($conn);
        $result = $userClass->loginCheck(strtolower($gebruikersnaam), $wachtwoord);

        if (is_array($result)) {
            $_SESSION["user_id"] = $result["id"];
            $_SESSION["gebruikersnaam"] = $result["gebruikersnaam"];
            $_SESSION["role"] = $result["rollen"];

            header("Location: ../index.php");
            exit;
        } else {
            $fout = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<h1>Inloggen</h1>

<?php if ($fout != "") { ?>
    <p><?php echo htmlspecialchars($fout); ?></p>
<?php } ?>

<form method="post">
    <label>Gebruikersnaam</label><br>
    <input type="text" name="gebruikersnaam" required><br><br>

    <label>Wachtwoord</label><br>
    <input type="password" name="wachtwoord" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
