<?php
require_once "../config/auth_check.php";
requireDirectie();

require_once "../config/Database.php";
require_once "../klasses/Verkoop.php";

$db = new Database();
$conn = $db->connect();

$verkoop = new Verkoop($conn);

$jaar = (int)($_GET["jaar"] ?? date("Y"));
$maand = (int)($_GET["maand"] ?? date("n"));

if ($maand < 1 || $maand > 12) {
    $maand = (int)date("n");
}

$omzet = $verkoop->getOmzetMaand($jaar, $maand);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Maand omzet</title>
</head>
<body>

<h1>Maand omzet</h1>

<form method="get">
    <label>Maand</label>
    <select name="maand">
        <?php for ($m = 1; $m <= 12; $m++) { ?>
            <option value="<?php echo $m; ?>" <?php if ($m === $maand) echo "selected"; ?>>
                <?php echo $m; ?>
            </option>
        <?php } ?>
    </select>

    <label>Jaar</label>
    <input type="number" name="jaar" value="<?php echo $jaar; ?>" min="2000" max="2100">

    <button type="submit">Toon</button>
</form>

<br>

<p>
    Totale omzet (ex btw):
    <strong>€ <?php echo number_format((float)$omzet, 2, ",", "."); ?></strong>
</p>

</body>
</html>
