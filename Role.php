<?php
class Role {
    private $conn;
    private $table_name = "role";

    public $id;
    public $nom;
    public $date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nom=:nom";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));

        $stmt->bindParam(":nom", $this->nom);

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
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        if ($stmt === false) {
            throw new Exception('Erreur lors de la préparation de la requête.');
        }
    
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            // Remplir les propriétés de l'objet Role avec les valeurs de la base de données
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->date = $row['date'];
            return $this;
        }
    
        return null; // Retourner null si aucun rôle n'a été trouvé
    }
    
    

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom = :nom WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nom', $this->nom);
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
