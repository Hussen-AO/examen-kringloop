<?php
$role = $_SESSION['role'] ?? '';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Bootstrap CSS (GEEN JS nodig) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Eigen CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>

<header class="app-navbar">
    <div class="header-container">
        <div class="header-row">

            <!-- Linkerkant: navigatie -->
            <nav class="nav-row">

                <strong class="navbar-brand text-white mb-0">Dashboard</strong>

                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="pages/artikel_overzicht.php">Artikelen</a>

                <?php if ($role === 'directie') { ?>
                    <!-- Admin dropdown -->
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            Admin ▾
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="pages/gebruiker_overzicht.php">Gebruikers</a></li>
                            <li><a href="pages/maand_overzicht.php">Maand overzicht</a></li>
                            <li><a href="pages/voorraad_overzicht.php">Winkelvoorraad</a></li>
                            <li><a href="pages/verkoop_overzicht.php">Verkopen</a></li>
                        </ul>
                    </div>

                    <a class="nav-link" href="pages/planning_overzicht.php">Ritplanning</a>

                <?php } elseif ($role === 'winkelpersoneel') { ?>
                    <a class="nav-link" href="pages/voorraad_overzicht.php">Winkelvoorraad</a>
                    <a class="nav-link" href="pages/verkoop_overzicht.php">Verkopen</a>
                    <a class="nav-link" href="pages/klant_overzicht.php">Klanten</a>

                <?php } elseif ($role === 'magazijnmedewerker') { ?>
                    <a class="nav-link" href="pages/voorraad_overzicht.php">Magazijnvoorraad</a>

                <?php } elseif ($role === 'chauffeur') { ?>
                    <a class="nav-link" href="pages/planning_overzicht.php">Ritplanning</a>
                <?php } ?>

            </nav>

            <!-- Rechterkant: rol + logout -->
            <div class="d-flex align-items-center gap-3">

                <span class="badge role-badge text-capitalize">
                    <?php echo htmlspecialchars($role); ?>
                </span>

                <form method="post" action="pages/logout.php" class="m-0">
                    <button type="submit" class="btn btn-outline-primary btn-sm logout-btn">
                        Uitloggen
                    </button>
                </form>

            </div>

        </div>
    </div>
</header>

</body>
</html>
