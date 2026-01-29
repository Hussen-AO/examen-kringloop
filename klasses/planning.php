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

    /**
     * Constructor - initialiseert de Planning klasse met een database verbinding
     * 
     * @param PDO $conn Database verbinding object
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Haalt alle klanten op uit de database voor gebruik in een dropdown menu
     * Geeft klanten gesorteerd op naam
     * 
     * @return array Associatieve array met id en naam van klanten
     */
    public function getKlanten() {
        $sql = "SELECT id, naam FROM klant ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle artikelen op uit de database voor gebruik in een dropdown menu
     * Geeft artikelen alfabetisch gesorteerd
     * 
     * @return array Associatieve array met id en naam van artikelen
     */
    public function getArtikelen() {
        $sql = "SELECT id, naam FROM artikel ORDER BY naam";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle planningsgegevens op uit de database
     * Kan optioneel gefilterd worden op type (ophalen of bezorgen)
     * Gegevens worden altijd gesorteerd op afspraakdatum
     * 
     * @param string $type Optioneel filter op ophalen_of_bezorgen veld
     * @return array Array met alle planning records inclusief klant- en artikelgegevens
     */
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

    /**
     * Voegt een nieuwe ritplanning toe aan de database
     * Slaat klantinfo, artikel, kenteken en afspraakgegevens op
     * 
     * @param int $klant_id ID van de klant
     * @param int $artikel_id ID van het artikel
     * @param string $kenteken Kenteken van het voertuig
     * @param string $type Type van vervoer (ophalen of bezorgen)
     * @param string $afspraak_op Datum en tijd van de afspraak
     * @return bool True als de insert succesvol was, false anderszins
     */
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
