<?php
session_start(); include "../config/database.php";
if ($_SESSION['role'] != 'admin') die("Access Denied");
$stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
header("Location: ../admin/menu_list.php");
?>