<?php
/*
Naam script     : Gebruiker.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Class voor gebruikers beheren
Auteur          : hussen
*/

class Gebruiker {

    public $conn; // database verbinding

    // constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // overzicht van gebruikers
    public function getOverzicht() {
        $sql = "SELECT id, gebruikersnaam, rollen FROM gebruiker ORDER BY gebruikersnaam";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 1 gebruiker ophalen
    public function getById($id) {
        $sql = "SELECT id, gebruikersnaam, rollen FROM gebruiker WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // check of gebruikersnaam al bestaat
    public function bestaatGebruikersnaam($gebruikersnaam) {
        $sql = "SELECT id FROM gebruiker WHERE LOWER(gebruikersnaam) = LOWER(:naam) LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":naam" => $gebruikersnaam]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? true : false;
    }

    // gebruiker toevoegen
    public function toevoegen($gebruikersnaam, $wachtwoord, $rollen) {
        $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

        $sql = "INSERT INTO gebruiker (gebruikersnaam, wachtwoord, rollen)
                VALUES (:naam, :pw, :rollen)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":naam" => $gebruikersnaam,
            ":pw" => $hash,
            ":rollen" => $rollen
        ]);
    }

    // gebruiker verwijderen
    public function verwijderen($id) {
        $sql = "DELETE FROM gebruiker WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>
