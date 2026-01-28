<?php
/*
Naam script     : Voorraad.php
Versie          : 1.2
Datum           : 28-01-2026
Beschrijving    : Class voor voorraad functies (CRUD)
Auteur          : 
*/

class Voorraad {

    public $conn; // database verbinding

    // constructor om database verbinding mee te geven
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // alle artikelen ophalen voor dropdown
    public function getArtikelen() {
        $sql = "SELECT id, naam FROM artikel ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // alle statussen ophalen
    public function getStatussen() {
        $sql = "SELECT id, status FROM status ORDER BY status";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // overzicht van voorraad ophalen met zoeken
    public function getOverzicht($zoek = "") {

        // als er niet gezocht wordt
        if ($zoek == "") {
            $sql = "SELECT v.id, a.id AS artikel_id, a.naam, v.aantal, v.locatie, s.status, v.ingeboekt_op
                    FROM voorraad v
                    JOIN artikel a ON v.artikel_id = a.id
                    JOIN status s ON v.status_id = s.id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        // als er wel gezocht wordt
        } else {
            $sql = "SELECT v.id, a.id AS artikel_id, a.naam, v.aantal, v.locatie, s.status, v.ingeboekt_op
                    FROM voorraad v
                    JOIN artikel a ON v.artikel_id = a.id
                    JOIN status s ON v.status_id = s.id
                    WHERE a.id = :zoek OR a.naam LIKE :zoeknaam";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":zoek" => $zoek,
                ":zoeknaam" => "%" . $zoek . "%"
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // 1 voorraad item ophalen
    public function getById($id) {
        $sql = "SELECT * FROM voorraad WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // voorraad toevoegen
    public function toevoegen($artikel_id, $aantal, $locatie, $status_id) {
        $sql = "INSERT INTO voorraad (artikel_id, locatie, aantal, status_id, ingeboekt_op)
                VALUES (:artikel_id, :locatie, :aantal, :status_id, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":artikel_id" => $artikel_id,
            ":locatie" => $locatie,
            ":aantal" => $aantal,
            ":status_id" => $status_id
        ]);
    }

    // voorraad wijzigen
    public function wijzigen($id, $aantal, $locatie, $status_id) {
        $sql = "UPDATE voorraad SET aantal = :aantal, locatie = :locatie, status_id = :status_id WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":aantal" => $aantal,
            ":locatie" => $locatie,
            ":status_id" => $status_id,
            ":id" => $id
        ]);
    }

    // voorraad verwijderen
    public function verwijderen($id) {
        $sql = "DELETE FROM voorraad WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>
