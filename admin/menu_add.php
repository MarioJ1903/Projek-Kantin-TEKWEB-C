<?php session_start(); if ($_SESSION['role'] != 'admin') die("Akses Ditolak."); ?>
<!DOCTYPE html>
<html>
<head><title>Tambah Menu</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="container" style="max-width:500px">
        <h3>Tambah Menu Baru</h3>
        <form action="../actions/menu_add_action.php" method="POST">
            <input type="text" name="name" placeholder="Nama Menu" required>
            <input type="number" name="price" placeholder="Harga" required>
            <input type="number" name="stock" placeholder="Stok" required>
            <button type="submit">Simpan</button>
        </form>
        <a href="menu_list.php">Batal</a>
    </div>
</body>
</html>