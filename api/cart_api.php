<?php
session_start();
header("Content-Type: application/json");
include_once '../config/Database.php';
include_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login dulu"]); exit;
}

$db = new Database();
$cart = new Cart($db->conn);
$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action == 'add') {
    $input = json_decode(file_get_contents("php://input"), true);
    if ($cart->addToCart($userId, $input['menu_id'])) {
        echo json_encode(["status" => "success"]);
    }
} elseif ($action == 'read') {
    $res = $cart->getCart($userId);
    $data = [];
    while($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}
?>