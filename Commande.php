<?php
class Commande {
    private $conn;
    private $table_name = "commande";

    public $id;
    public $produit_id;
    public $user_id;
    public $etat_cmd;
    public $est_paye;
    public $est_livre;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET produit_id=:produit_id, user_id=:user_id, etat_cmd=:etat_cmd, est_paye=:est_paye, est_livre=:est_livre";

        $stmt = $this->conn->prepare($query);

        $this->produit_id = htmlspecialchars(strip_tags($this->produit_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->etat_cmd = htmlspecialchars(strip_tags($this->etat_cmd));
        $this->est_paye = htmlspecialchars(strip_tags($this->est_paye));
        $this->est_livre = htmlspecialchars(strip_tags($this->est_livre));

        $stmt->bindParam(":produit_id", $this->produit_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":etat_cmd", $this->etat_cmd);
        $stmt->bindParam(":est_paye", $this->est_paye);
        $stmt->bindParam(":est_livre", $this->est_livre);

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
        $query = "UPDATE " . $this->table_name . " SET produit_id=:produit_id, user_id=:user_id, etat_cmd=:etat_cmd, est_paye=:est_paye, est_livre=:est_livre WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->produit_id = htmlspecialchars(strip_tags($this->produit_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->etat_cmd = htmlspecialchars(strip_tags($this->etat_cmd));
        $this->est_paye = htmlspecialchars(strip_tags($this->est_paye));
        $this->est_livre = htmlspecialchars(strip_tags($this->est_livre));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":produit_id", $this->produit_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":etat_cmd", $this->etat_cmd);
        $stmt->bindParam(":est_paye", $this->est_paye);
        $stmt->bindParam(":est_livre", $this->est_livre);
        $stmt->bindParam(":id", $this->id);

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
