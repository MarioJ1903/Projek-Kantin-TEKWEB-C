<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') die("Akses Ditolak.");

// Sanitasi ID agar aman dari SQL Injection
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM menu WHERE menu_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Data menu tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width:500px">
        <h3>Edit Menu</h3>
        <form action="../actions/menu_edit_action.php" method="POST">
            <input type="hidden" name="menu_id" value="<?= $data['menu_id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
            <input type="number" name="price" value="<?= $data['price'] ?>" required>
            <input type="number" name="stock" value="<?= $data['stock'] ?>" required>
            <button type="submit">Update</button>
        </form>
        <a href="menu_list.php">Batal</a>
    </div>
</body>
</html>