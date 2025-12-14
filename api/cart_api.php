<?php
session_start();
header("Content-Type: application/json");
include_once '../config/Database.php';
include_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Silakan login"]);
    exit;
}

$db = new Database();
$cart = new Cart($db->conn);
$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action == 'add') {
    $input = json_decode(file_get_contents("php://input"), true);
    $qty = isset($input['quantity']) ? (int)$input['quantity'] : 1;
    if ($qty < 1) $qty = 1;

    if ($cart->addToCart($userId, $input['menu_id'], $qty)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} 
// --- HANDLER UPDATE ---
elseif ($action == 'update') {
    $input = json_decode(file_get_contents("php://input"), true);
    if ($cart->updateQuantity($userId, $input['menu_id'], (int)$input['quantity'])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
elseif ($action == 'read') {
    $res = $cart->getCart($userId);
    $data = [];
    while($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}
?>