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

    // --- FITUR BARU: UPDATE MENU ---
    public function update($id, $name, $price, $stock, $image) {
        if ($image) {
            // Jika ada gambar baru, update semua termasuk gambar
            $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET name=?, price=?, stock=?, image=? WHERE menu_id=?");
            $stmt->bind_param("siisi", $name, $price, $stock, $image, $id);
        } else {
            // Jika tidak ada gambar baru, jangan ubah kolom image
            $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET name=?, price=?, stock=? WHERE menu_id=?");
            $stmt->bind_param("siii", $name, $price, $stock, $id);
        }
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE menu_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function reduceStock($id, $qty) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET stock = stock - ? WHERE menu_id = ?");
        $stmt->bind_param("ii", $qty, $id);
        return $stmt->execute();
    }
}
?>