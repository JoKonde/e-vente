<?php
class Produit {
    private $conn;
    private $table_name = "produit";

    public $id;
    public $nom;
    public $categorie_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nom=:nom, categorie_id=:categorie_id";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":categorie_id", $this->categorie_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom = :nom, categorie_id = :categorie_id WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
