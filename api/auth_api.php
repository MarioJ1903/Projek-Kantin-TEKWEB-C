<?php
// File: api/auth_api.php
session_start();
header("Content-Type: application/json");

// Sertakan file database dan user
include_once '../config/Database.php';
include_once '../classes/User.php';

$db = new Database();
$user = new User($db->conn);

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? $_POST['remember'] : false; // Cek checkbox Ingat Saya

    $userData = $user->login($email, $password);

    if ($userData) {
        // 1. Simpan Sesi (Wajib)
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['role'] = $userData['role'];
        $_SESSION['name'] = $userData['name'];

        // 2. Fitur Ingat Saya (Cookie)
        if ($remember === 'true') { // Javascript mengirim string 'true'
            // Buat Cookie berlaku 30 hari
            setcookie('user_login', $userData['user_id'], time() + (86400 * 30), "/");
        }

        echo json_encode([
            "status" => "success", 
            "role" => $userData['role'],
            "message" => "Login Berhasil!"
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Email atau Password salah!"
        ]);
    }
} 
elseif ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... kode register sama seperti sebelumnya ...
    $res = $user->register($_POST['name'], $_POST['email'], $_POST['password']);
    if ($res === true) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $res]);
    }
} 
elseif ($action == 'logout') {
    // Hapus Session
    session_destroy();
    // Hapus Cookie Ingat Saya
    setcookie('user_login', '', time() - 3600, "/");
    header("Location: ../public/login.php");
}
?>