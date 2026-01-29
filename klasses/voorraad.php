<?php
/*
Naam script     : Voorraad.php
Versie          : 1.2
Datum           : 28-01-2026
Beschrijving    : Class voor voorraad functies (CRUD)
Auteur          : jayjay stam 
*/

class Voorraad {

    public $conn; // database verbinding

    /**
     * Constructor - initialiseert de Voorraad klasse met een database verbinding
     * 
     * @param PDO $conn Database verbinding object
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Haalt alle artikelen op uit de database voor gebruik in een dropdown menu
     * Geeft artikelen gesorteerd op naam
     * 
     * @return array
     */
    public function getArtikelen() {
        $sql = "SELECT id, naam FROM artikel ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle statussen op uit de database voor gebruik in een dropdown menu
     * Geeft statussen alfabetisch gesorteerd
     * 
     * @return array 
     */
    public function getStatussen() {
        $sql = "SELECT id, status FROM status ORDER BY status";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle voorraadgegevens op uit de database
     * Kan optioneel gefilterd worden op artikel id of artikel naam
     * 
     * @param string $zoek 
     * @return array 
     */
    public function getOverzicht($zoek = "") {
        // Als er niet gezocht wordt, return alles
        if ($zoek == "") {
            $sql = "SELECT v.id, a.id AS artikel_id, a.naam, v.aantal, v.locatie, s.status, v.ingeboekt_op
                    FROM voorraad v
                    JOIN artikel a ON v.artikel_id = a.id
                    JOIN status s ON v.status_id = s.id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Als er wel gezocht wordt op artikel id of naam
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

    /**
     * Haalt een enkel voorraad item op aan de hand van het ID
     * 
     * @param int $id 
     * @return array 
     */
    public function getById($id) {
        $sql = "SELECT * FROM voorraad WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Voegt een nieuw voorraad item toe aan de database
     * Slaat artikel, aantal, locatie en status op met huidige timestamp
     * 
     * @param int $artikel_id 
     * @param int $aantal 
     * @param string $locatie 
     * @param int $status_id 
     * @return bool 
     */
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

    /**
     * Wijzigt een bestaand voorraad item in de database
     * Update aantal, locatie en status voor het gegeven ID
     * 
     * @param int $id 
     * @param int $aantal 
     * @param string $locatie 
     * @param int $status_id 
     * @return bool 
     */
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

    /**
     * Verwijdert een voorraad item uit de database
     * 
     * @param int $id 
     * @return bool 
     */
    public function verwijderen($id) {
        $sql = "DELETE FROM voorraad WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>
