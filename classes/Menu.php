<?php
class Menu {
    private $conn;
    private $table = "menu";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $result = $this->conn->query("SELECT * FROM " . $this->table . " ORDER BY menu_id DESC");
        return $result;
    }

    public function create($name, $price, $stock, $image) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (name, price, stock, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $name, $price, $stock, $image);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE menu_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Tambahan untuk mengurangi stok saat checkout (dipakai di Order Class)
    public function reduceStock($id, $qty) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET stock = stock - ? WHERE menu_id = ?");
        $stmt->bind_param("ii", $qty, $id);
        return $stmt->execute();
    }
}
?>