<?php
session_start();
include "../config/database.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Casting ke int untuk keamanan (Mencegah SQL Injection)
$menu_id = (int)$_POST['menu_id'];

// Cek apakah barang sudah ada di keranjang user tersebut
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND menu_id = ?");
$stmt->bind_param("ii", $user_id, $menu_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Jika ada, update quantity (+1)
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND menu_id = ?");
    $update->bind_param("ii", $user_id, $menu_id);
    $update->execute();
} else {
    // Jika belum ada, insert baru
    $insert = $conn->prepare("INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $menu_id);
    $insert->execute();
}

header("Location: ../public/cart.php");
?>