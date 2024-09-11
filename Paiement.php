<?php
class Paiement {
    private $conn;
    private $table_name = "paiement";

    public $id;
    public $montant;
    public $commande_id;
    public $user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET montant=:montant, commande_id=:commande_id, user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        $this->montant = htmlspecialchars(strip_tags($this->montant));
        $this->commande_id = htmlspecialchars(strip_tags($this->commande_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(":montant", $this->montant);
        $stmt->bindParam(":commande_id", $this->commande_id);
        $stmt->bindParam(":user_id", $this->user_id);

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
        $query = "UPDATE " . $this->table_name . " SET montant=:montant, commande_id=:commande_id, user_id=:user_id WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->montant = htmlspecialchars(strip_tags($this->montant));
        $this->commande_id = htmlspecialchars(strip_tags($this->commande_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':montant', $this->montant);
        $stmt->bindParam(':commande_id', $this->commande_id);
        $stmt->bindParam(':user_id', $this->user_id);
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
