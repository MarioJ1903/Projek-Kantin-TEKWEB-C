<?php
session_start();
include "../config/database.php";

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mulai Transaksi Database
$conn->begin_transaction();

try {
    // 1. Ambil data keranjang JOIN dengan menu untuk mendapatkan harga ASLI dan stok terbaru
    $query = "SELECT c.menu_id, c.quantity, m.price, m.stock, m.name 
              FROM cart c 
              JOIN menu m ON c.menu_id = m.menu_id 
              WHERE c.user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = [];
    $total_calculated = 0;

    // 2. Validasi stok dan hitung total
    while ($row = $result->fetch_assoc()) {
        if ($row['stock'] < $row['quantity']) {
            throw new Exception("Stok untuk menu '{$row['name']}' tidak mencukupi (Sisa: {$row['stock']}).");
        }
        $total_calculated += ($row['price'] * $row['quantity']);
        $cart_items[] = $row;
    }

    if (empty($cart_items)) {
        throw new Exception("Keranjang belanja kosong.");
    }

    // 3. Buat pesanan di tabel orders
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
    $stmt_order->bind_param("ii", $user_id, $total_calculated);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    // 4. Masukkan item ke order_items dan kurangi stok menu
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE menu SET stock = stock - ? WHERE menu_id = ?");

    foreach ($cart_items as $item) {
        // Insert item details
        $stmt_item->bind_param("iiii", $order_id, $item['menu_id'], $item['quantity'], $item['price']);
        $stmt_item->execute();

        // Potong stok
        $stmt_stock->bind_param("ii", $item['quantity'], $item['menu_id']);
        $stmt_stock->execute();
    }

    // 5. Kosongkan keranjang user
    $stmt_del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_del->bind_param("i", $user_id);
    $stmt_del->execute();

    // Jika semua lancar, simpan perubahan
    $conn->commit();
    header("Location: ../public/orders.php");

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    $conn->rollback();
    // Redirect kembali dengan pesan error (bisa ditambahkan alert JS di halaman cart)
    echo "<script>alert('Gagal Checkout: " . $e->getMessage() . "'); window.location = '../public/cart.php';</script>";
}
?>