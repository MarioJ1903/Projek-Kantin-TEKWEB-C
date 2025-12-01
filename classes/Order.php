<?php
// File: classes/Order.php
class Order {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkout($userId) {
        $this->conn->begin_transaction();
        try {
            // Panggil class Cart dan Menu di dalam fungsi ini
            // Pastikan file ini di-include di api/order_api.php
            $cartClass = new Cart($this->conn);
            $menuClass = new Menu($this->conn);

            $cartItems = $cartClass->getCart($userId);
            
            $total = 0;
            $items = [];
            
            // 1. Validasi Stok & Hitung Total
            while($row = $cartItems->fetch_assoc()){
                if($row['stock'] < $row['quantity']) {
                    throw new Exception("Stok {$row['name']} habis! Sisa: {$row['stock']}");
                }
                $total += ($row['price'] * $row['quantity']);
                $items[] = $row;
            }

            if(empty($items)) throw new Exception("Keranjang kosong!");

            // 2. Insert ke Tabel Orders (Status Langsung 'selesai')
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'selesai')");
            $stmt->bind_param("ii", $userId, $total);
            $stmt->execute();
            $orderId = $this->conn->insert_id;

            // 3. Masukkan Item & KURANGI STOK
            $stmtItem = $this->conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            foreach($items as $item){
                // Insert Item
                $stmtItem->bind_param("iiii", $orderId, $item['menu_id'], $item['quantity'], $item['price']);
                $stmtItem->execute();

                // Kurangi Stok (Penting!)
                $menuClass->reduceStock($item['menu_id'], $item['quantity']); // Pastikan method reduceStock ada di classes/Menu.php
            }

            // 4. Hapus Keranjang
            $cartClass->clearCart($userId);
            
            $this->conn->commit();
            return ["status" => true];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public function getHistory($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>