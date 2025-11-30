<?php
session_start();
include "../config/database.php";

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Cek apakah email sudah terdaftar
$check = $conn->prepare("SELECT email FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo "<script>alert('Email sudah terdaftar!'); window.location='../public/register.php';</script>";
    exit();
}

// Lanjut proses daftar
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashed);

if ($stmt->execute()) {
    echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='../public/login.php';</script>";
} else {
    echo "Gagal daftar: " . $conn->error;
}
?>