<?php
session_start(); include "../config/database.php";
if ($_SESSION['role'] != 'admin') die("Access Denied");
$stmt = $conn->prepare("INSERT INTO menu (name, price, stock) VALUES (?, ?, ?)");
$stmt->bind_param("sii", $_POST['name'], $_POST['price'], $_POST['stock']);
$stmt->execute();
header("Location: ../admin/menu_list.php");
?>