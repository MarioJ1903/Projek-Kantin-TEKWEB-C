<?php
// File: config/auto_login.php

// Cek apakah session sudah dimulai, jika belum start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi database (karena file ini ada di folder config, panggil Database.php langsung)
include_once 'Database.php';
// Panggil class User (keluar satu folder dulu)
include_once '../classes/User.php';

// LOGIKA UTAMA:
// Jika User BELUM Login (Session kosong) TAPI Punya Cookie 'user_login'
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login'])) {
    
    $database = new Database();
    $db = $database->conn;
    $userObj = new User($db);
    
    // Ambil ID dari Cookie
    $cookie_id = $_COOKIE['user_login'];
    
    // Cari data user di database berdasarkan ID tersebut
    $userData = $userObj->getUserById($cookie_id);

    // Jika datanya valid/ditemukan
    if ($userData) {
        // Buatkan Session secara otomatis (Auto Login)
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['role'] = $userData['role'];
        $_SESSION['name'] = $userData['name'];
    }
}
?>