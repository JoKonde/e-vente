<?php
class User {
    private $conn;
    private $table_name = "user";

    public $id;
    public $email;
    public $password;
    public $etat;
    public $role_id;
    public $noms;
    public $sexe;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, password=:password, role_id=:role_id, noms=:noms, sexe=:sexe";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->noms = htmlspecialchars(strip_tags($this->noms));
        $this->sexe = htmlspecialchars(strip_tags($this->sexe));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":noms", $this->noms);
        $stmt->bindParam(":sexe", $this->sexe);

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
        $query = "UPDATE " . $this->table_name . " SET email = :email, password = :password, etat = :etat, role_id = :role_id, noms = :noms, sexe = :sexe WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->etat = htmlspecialchars(strip_tags($this->etat));
        $this->noms = htmlspecialchars(strip_tags($this->noms));
        $this->sexe = htmlspecialchars(strip_tags($this->sexe));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindParam(':etat', $this->etat);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':noms', $this->noms);
        $stmt->bindParam(':role_id', $this->role_id);
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

    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($this->password, $user['password'])) {
            foreach ($user as $key => $value) {
                $this->$key = $value;
            }
            return $this;
        }
        return false;
    }
}
?>
