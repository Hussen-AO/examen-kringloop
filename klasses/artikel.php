<?php
/*
Naam script     : Artikel.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Class voor artikelen beheren (CRUD)
Auteur          : Sam 
*/

class Artikel {

    public $conn; // database verbinding

    // constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // categorieën ophalen voor dropdown
    public function getCategorieen() {
        $sql = "SELECT id, categorie FROM categorie ORDER BY categorie";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // overzicht ophalen (met zoeken)
    public function getOverzicht($zoek = "") {

        if ($zoek == "") {
            $sql = "SELECT a.id, a.naam, a.prijs_ex_btw, c.categorie
                    FROM artikel a
                    JOIN categorie c ON a.categorie_id = c.id
                    ORDER BY a.naam";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT a.id, a.naam, a.prijs_ex_btw, c.categorie
                FROM artikel a
                JOIN categorie c ON a.categorie_id = c.id
                WHERE a.id = :zoek OR a.naam LIKE :zoeknaam
                ORDER BY a.naam";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":zoek" => $zoek,
            ":zoeknaam" => "%" . $zoek . "%"
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 1 artikel ophalen
    public function getById($id) {
        $sql = "SELECT * FROM artikel WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // artikel toevoegen
    public function toevoegen($categorie_id, $naam, $prijs_ex_btw) {
        $sql = "INSERT INTO artikel (categorie_id, naam, prijs_ex_btw)
                VALUES (:categorie_id, :naam, :prijs)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":categorie_id" => $categorie_id,
            ":naam" => $naam,
            ":prijs" => $prijs_ex_btw
        ]);
    }

    // artikel wijzigen
    public function wijzigen($id, $categorie_id, $naam, $prijs_ex_btw) {
        $sql = "UPDATE artikel
                SET categorie_id = :categorie_id, naam = :naam, prijs_ex_btw = :prijs
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":categorie_id" => $categorie_id,
            ":naam" => $naam,
            ":prijs" => $prijs_ex_btw,
            ":id" => $id
        ]);
    }

    // artikel verwijderen
    public function verwijderen($id) {
        $sql = "DELETE FROM artikel WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }

}
?>
