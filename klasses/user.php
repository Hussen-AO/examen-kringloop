<?php
/*
Naam script     : User.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : User class voor inloggen met database
Auteur          : tijs
*/

class User {

    // Database verbinding
    public $conn;

    // Constructor - initialiseert de database verbinding
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Haalt een gebruiker op uit de database op basis van gebruikersnaam
    public function getUserByName($gebruikersnaam) {
        // SQL query om gebruiker te zoeken
        $sql = "SELECT * FROM gebruiker WHERE gebruikersnaam = :naam LIMIT 1";
        
        // Prepare en execute de query met parameter binding voor veiligheid
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':naam' => $gebruikersnaam]);
        
        // Geef de gebruiker terug als associatieve array
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verifieert login gegevens (gebruikersnaam en wachtwoord)
    public function loginCheck($gebruikersnaam, $wachtwoord) {
        // Zoek de gebruiker op basis van gebruikersnaam
        $user = $this->getUserByName($gebruikersnaam);

        // Controleer of gebruiker bestaat
        if (!$user) {
            return "Gebruikersnaam bestaat niet.";
        }

        // Controleer of wachtwoord correct is (vergelijk met gehashed wachtwoord)
        if (!password_verify($wachtwoord, $user['wachtwoord'])) {
            return "Wachtwoord is onjuist.";
        }

        // Geef gebruiker terug bij succesvolle login
        return $user;
    }
}
?>
