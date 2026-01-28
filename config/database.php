<?php
/*
Naam script     : Database.php
auteur          : hussen
Versie          : 1.0
Datum           : 28-01-2026
Beschrijving    : Database class voor verbinding met MySQL met OOP
*/

class Database {

    private $host = "localhost";
    private $dbname = "duurzaam";
    private $username = "root";
    private $password = "";

    public function connect() {
        try {
            return new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                $this->username,
                $this->password
            );
        } catch (PDOException $e) {
            die("Fout met de database: " . $e->getMessage());
        }
    }
}
?>