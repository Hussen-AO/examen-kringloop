<?php
/*
Naam script     : Planning.php
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Class voor ritplanning (overzicht en toevoegen)
Auteur          : jayjay stam
*/

class Planning {

    public $conn; // database verbinding

    public function __construct($conn) {
        $this->conn = $conn;
    }

  
    public function getKlanten() {
        $sql = "SELECT id, naam FROM klant ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getArtikelen() {
        $sql = "SELECT id, naam FROM artikel ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOverzicht($type = "") {
        // Als geen filter opgegeven, return alle planningen
        if ($type == "") {
            $sql = "SELECT p.id, k.naam AS klant, a.naam AS artikel, p.kenteken, p.ophalen_of_bezorgen, p.afspraak_op
                    FROM planning p
                    JOIN klant k ON p.klant_id = k.id
                    JOIN artikel a ON p.artikel_id = a.id
                    ORDER BY p.afspraak_op ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Filter op opgegeven type (ophalen of bezorgen)
        $sql = "SELECT p.id, k.naam AS klant, a.naam AS artikel, p.kenteken, p.ophalen_of_bezorgen, p.afspraak_op
                FROM planning p
                JOIN klant k ON p.klant_id = k.id
                JOIN artikel a ON p.artikel_id = a.id
                WHERE p.ophalen_of_bezorgen = :type
                ORDER BY p.afspraak_op ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":type" => $type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function toevoegen($klant_id, $artikel_id, $kenteken, $type, $afspraak_op) {
        $sql = "INSERT INTO planning (artikel_id, klant_id, kenteken, ophalen_of_bezorgen, afspraak_op)
                VALUES (:artikel_id, :klant_id, :kenteken, :type, :afspraak_op)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":artikel_id" => $artikel_id,
            ":klant_id" => $klant_id,
            ":kenteken" => $kenteken,
            ":type" => $type,
            ":afspraak_op" => $afspraak_op
        ]);
    }
}
?>
