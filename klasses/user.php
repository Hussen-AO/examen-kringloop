<?php
/*
Naam script     : User.php
Versie          : 1.1
Datum           : 28-01-2026
Beschrijving    : User class voor inloggen met database
Auteur          : tijs
*/

class User {

    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserByName($gebruikersnaam) {
        $sql = "SELECT * FROM gebruiker WHERE gebruikersnaam = :naam LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':naam' => $gebruikersnaam]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function loginCheck($gebruikersnaam, $wachtwoord) {
        $user = $this->getUserByName($gebruikersnaam);

        if (!$user) {
            return "Gebruikersnaam bestaat niet.";
        }

        if (!password_verify($wachtwoord, $user['wachtwoord'])) {
            return "Wachtwoord is onjuist.";
        }

        return $user;
    }
}
?>
