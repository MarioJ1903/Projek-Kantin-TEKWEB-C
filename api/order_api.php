<?php
// File: api/order_api.php
session_start();
header("Content-Type: application/json");

include_once '../config/Database.php';
include_once '../classes/Cart.php';
include_once '../classes/Menu.php';
include_once '../classes/Order.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Silakan login terlebih dahulu"]);
    exit;
}

$database = new Database();
$db = $database->conn;
$order = new Order($db);

$userId = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'checkout') {
    // Panggil fungsi checkout yang sudah kita buat
    $result = $order->checkout($userId);
    echo json_encode($result);
} 
elseif ($action == 'history') {
    // Untuk halaman history
    $result = $order->getHistory($userId);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}
?>