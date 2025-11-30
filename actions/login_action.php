<?php
session_start();
include "../config/database.php";
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role'] = $row['role'];
        if ($row['role'] == 'admin') { header("Location: ../admin/menu_list.php"); }
        else { header("Location: ../public/menu.php"); }
        exit();
    }
}
header("Location: ../public/login.php?error=1");
?>