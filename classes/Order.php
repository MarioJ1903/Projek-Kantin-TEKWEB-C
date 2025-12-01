<?php
// File: classes/Order.php

class Order {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Fungsi Checkout (User Bayar)
    public function checkout($userId) {
        $this->conn->begin_transaction();
        try {
            // Include class dependency di dalam method
            require_once 'Cart.php';
            require_once 'Menu.php';

            $cartClass = new Cart($this->conn);
            $menuClass = new Menu($this->conn);

            $cartItems = $cartClass->getCart($userId);
            
            $total = 0;
            $items = [];
            
            // Validasi Stok
            while($row = $cartItems->fetch_assoc()){
                if($row['stock'] < $row['quantity']) {
                    throw new Exception("Stok {$row['name']} habis! Sisa: {$row['stock']}");
                }
                $total += ($row['price'] * $row['quantity']);
                $items[] = $row;
            }

            if(empty($items)) throw new Exception("Keranjang kosong!");

            // Buat Order (Status langsung 'selesai')
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'selesai')");
            $stmt->bind_param("ii", $userId, $total);
            $stmt->execute();
            $orderId = $this->conn->insert_id;

            // Masukkan Item & Kurangi Stok
            $stmtItem = $this->conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            foreach($items as $item){
                $stmtItem->bind_param("iiii", $orderId, $item['menu_id'], $item['quantity'], $item['price']);
                $stmtItem->execute();
                $menuClass->reduceStock($item['menu_id'], $item['quantity']); 
            }

            $cartClass->clearCart($userId);
            
            $this->conn->commit();
            return ["status" => true];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    // 2. Riwayat User (Hanya pesanannya sendiri)
    public function getHistory($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // 3. [BARU] Riwayat Admin (Semua pesanan + Nama User)
    public function getAllOrders() {
        $query = "SELECT o.*, u.name as user_name 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.user_id 
                  ORDER BY o.created_at DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // 4. [BARU] Detail Pesanan (Item apa saja yg dibeli)
    public function getOrderDetails($orderId) {
        $query = "SELECT oi.*, m.name, m.image 
                  FROM order_items oi 
                  JOIN menu m ON oi.menu_id = m.menu_id 
                  WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>