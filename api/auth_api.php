<?php
session_start();
header("Content-Type: application/json");
include_once '../config/Database.php';
include_once '../classes/User.php';

$db = new Database();
$user = new User($db->conn);
$action = $_GET['action'] ?? '';

if ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $user->login($_POST['email'], $_POST['password']);
    if ($data) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['role'] = $data['role'];
        echo json_encode(["status" => "success", "role" => $data['role']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email atau Password salah!"]);
    }
} elseif ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $res = $user->register($_POST['name'], $_POST['email'], $_POST['password']);
    if ($res === true) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $res]);
    }
} elseif ($action == 'logout') {
    session_destroy();
    header("Location: ../public/login.php");
}
?>