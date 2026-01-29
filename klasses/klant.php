<?php
/*
Naam script     : Klant.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Class voor klanten/persoonsgegevens (CRUD)
Auteur          : Sam
*/

class Klant {

    public $conn; // database verbinding

    // constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // overzicht ophalen
    public function getOverzicht($zoek = "") {

        if ($zoek == "") {
            $sql = "SELECT * FROM klant ORDER BY naam";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT * FROM klant
                WHERE naam LIKE :zoek OR email LIKE :zoek
                ORDER BY naam";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":zoek" => "%" . $zoek . "%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 1 klant ophalen
    public function getById($id) {
        $sql = "SELECT * FROM klant WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // klant toevoegen
    public function toevoegen($naam, $adres, $plaats, $telefoon, $email) {
        $sql = "INSERT INTO klant (naam, adres, plaats, telefoon, email)
                VALUES (:naam, :adres, :plaats, :telefoon, :email)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":naam" => $naam,
            ":adres" => $adres,
            ":plaats" => $plaats,
            ":telefoon" => $telefoon,
            ":email" => $email
        ]);
    }

    // klant wijzigen
    public function wijzigen($id, $naam, $adres, $plaats, $telefoon, $email) {
        $sql = "UPDATE klant
                SET naam = :naam, adres = :adres, plaats = :plaats, telefoon = :telefoon, email = :email
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":naam" => $naam,
            ":adres" => $adres,
            ":plaats" => $plaats,
            ":telefoon" => $telefoon,
            ":email" => $email,
            ":id" => $id
        ]);
    }

    // klant verwijderen
    public function verwijderen($id) {
        $sql = "DELETE FROM klant WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>
