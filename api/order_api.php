<?php
// File: api/order_api.php
session_start();
header("Content-Type: application/json");

include_once '../config/Database.php';
include_once '../classes/Order.php';

// Wajib Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Akses ditolak"]);
    exit;
}

$database = new Database();
$db = $database->conn;
$order = new Order($db);

$userId = $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'checkout') {
    echo json_encode($order->checkout($userId));
} 
elseif ($action == 'history') {
    // History User
    $result = $order->getHistory($userId);
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}
elseif ($action == 'admin_history') {
    // History Admin (Cek Role)
    if ($role === 'admin') {
        $result = $order->getAllOrders();
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
}
elseif ($action == 'details' && isset($_GET['order_id'])) {
    // Detail Item (Bisa diakses User & Admin)
    $result = $order->getOrderDetails($_GET['order_id']);
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}
?>