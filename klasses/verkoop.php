<?php
/*
Naam script     : Verkoop.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Class voor verkopen (overzicht en toevoegen)
Auteur          : hussen
*/

class Verkoop {

    public $conn; // database verbinding

    // constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // klanten ophalen voor dropdown
    public function getKlanten() {
        $sql = "SELECT id, naam FROM klant ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // artikelen ophalen voor dropdown
    public function getArtikelen() {
        $sql = "SELECT id, naam, prijs_ex_btw FROM artikel ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // verkopen overzicht ophalen (met filter)
    public function getOverzicht($van = "", $tot = "") {

        // als er geen filter is
        if ($van == "" && $tot == "") {
            $sql = "SELECT v.id, k.naam AS klant, a.naam AS artikel, a.prijs_ex_btw, v.verkocht_op
                    FROM verkopen v
                    JOIN klant k ON v.klant_id = k.id
                    JOIN artikel a ON v.artikel_id = a.id
                    ORDER BY v.verkocht_op DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // lege datums opvangen
        if ($van == "") $van = "1900-01-01";
        if ($tot == "") $tot = "2999-12-31";

        $sql = "SELECT v.id, k.naam AS klant, a.naam AS artikel, a.prijs_ex_btw, v.verkocht_op
                FROM verkopen v
                JOIN klant k ON v.klant_id = k.id
                JOIN artikel a ON v.artikel_id = a.id
                WHERE DATE(v.verkocht_op) >= :van AND DATE(v.verkocht_op) <= :tot
                ORDER BY v.verkocht_op DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":van" => $van,
            ":tot" => $tot
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // verkoop toevoegen
    public function toevoegen($klant_id, $artikel_id) {
        $sql = "INSERT INTO verkopen (klant_id, artikel_id, verkocht_op)
                VALUES (:klant_id, :artikel_id, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":klant_id" => $klant_id,
            ":artikel_id" => $artikel_id
        ]);
    }
 public function getOmzetMaand(int $jaar, int $maand): float
{
    $sql = "
        SELECT COALESCE(SUM(a.prijs_ex_btw), 0) AS omzet
        FROM verkopen v
        JOIN artikel a ON a.id = v.artikel_id
        WHERE YEAR(v.verkocht_op) = :jaar
          AND MONTH(v.verkocht_op) = :maand
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ":jaar" => $jaar,
        ":maand" => $maand
    ]);

    return (float) $stmt->fetchColumn();
}

}
?>
