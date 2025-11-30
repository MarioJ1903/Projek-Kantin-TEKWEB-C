<?php
session_start(); include "../config/database.php";
if ($_SESSION['role'] != 'admin') die("Access Denied");
$stmt = $conn->prepare("UPDATE menu SET name=?, price=?, stock=? WHERE menu_id=?");
$stmt->bind_param("siii", $_POST['name'], $_POST['price'], $_POST['stock'], $_POST['menu_id']);
$stmt->execute();
header("Location: ../admin/menu_list.php");
?>