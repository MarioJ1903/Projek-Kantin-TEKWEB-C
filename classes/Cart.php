<?php
// File: classes/Cart.php
class Cart {
    private $conn;
    private $table = "cart";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addToCart($userId, $menuId) {
        // Cek duplikat
        $check = $this->conn->prepare("SELECT quantity FROM " . $this->table . " WHERE user_id = ? AND menu_id = ?");
        $check->bind_param("ii", $userId, $menuId);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET quantity = quantity + 1 WHERE user_id = ? AND menu_id = ?");
        } else {
            $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (user_id, menu_id, quantity) VALUES (?, ?, 1)");
        }
        $stmt->bind_param("ii", $userId, $menuId);
        return $stmt->execute();
    }

    public function getCart($userId) {
        $query = "SELECT c.*, m.name, m.price, m.image, m.stock FROM " . $this->table . " c JOIN menu m ON c.menu_id = m.menu_id WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function clearCart($userId) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}
?>