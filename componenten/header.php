<?php
// Haal de rol van de huidige gebruiker op uit de sessie
$role = $_SESSION['role'] ?? '';
?>

<header style="display:flex; justify-content:space-between; align-items:center; padding:15px; border-bottom:1px solid #ddd;">
    
    <nav style="display:flex; gap:15px; align-items:center;">
        <strong>Dashboard</strong>

        <?php if ($role === 'directie') { ?>
            <a href="index.php">Home</a>
            <a href="pages/artikel_overzicht.php">Artikelen</a>
            <a href="pages/gebruiker_overzicht.php">gebruiker overzicht</a>
            <a href="pages/klant_overzicht.php">Klanten</a>
            <a href="pages/maand_overzicht.php">Maand overzicht</a>
            <a href="pages/voorraad_overzicht.php">Winkelvoorraad</a>
            <a href="pages/verkoop_overzicht.php">Verkopen</a>
            <a href="pages/planning_overzicht.php">Ritplanning</a>

        <?php } elseif ($role === 'winkelpersoneel') { ?>
            <a href="index.php">Home</a>
            <a href="pages/artikel_overzicht.php">Artikelen</a>
            <a href="pages/voorraad_overzicht.php">Winkelvoorraad</a>
            <a href="pages/verkoop_overzicht.php">Verkopen</a>
            <a href="pages/klant_overzicht.php">Klanten</a>

        <?php } elseif ($role === 'magazijnmedewerker') { ?>
            <a href="index.php">Home</a>
            <a href="pages/voorraad_overzicht.php">Magazijn voorraad</a>


        <?php } elseif ($role === 'chauffeur') { ?>
            <a href="index.php">Home</a>
            <a href="pages/planning_overzicht.php">Ritplanning</a>
        <?php } ?>
    </nav>

    <!-- Gebruiker info en uitlog knop -->
    <div style="display:flex; align-items:center; gap:10px;">
        <!-- Toon huidige rol van de gebruiker -->
        <small><?php echo htmlspecialchars($role); ?></small>

        <form method="post" action="pages/logout.php" style="margin:0;">
            <button type="submit">Uitloggen</button>
        </form>
    </div>

</header>
