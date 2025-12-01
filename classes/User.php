<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($name, $email, $password) {
        // Cek Email
        $check = $this->conn->prepare("SELECT email FROM " . $this->table . " WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) return "Email sudah terdaftar!";

        // Insert
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);
        
        if ($stmt->execute()) return true;
        return "Gagal mendaftar.";
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                return $row; // Login Sukses
            }
        }
        return false;
    }
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>