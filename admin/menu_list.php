<?php
session_start();
include "../config/database.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') die("Akses Ditolak.");
$data = mysqli_query($conn, "SELECT * FROM menu");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Kelola Menu</h2>
        <a href="menu_add.php" class="button">Tambah Menu</a> | 
        <a href="../actions/logout.php" style="color:red">Logout</a>
        <table>
            <tr><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
            <?php while($m = mysqli_fetch_assoc($data)){ ?>
            <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td>Rp <?= number_format($m['price']) ?></td>
                <td><?= $m['stock'] ?></td>
                <td>
                    <a href="menu_edit.php?id=<?= $m['menu_id'] ?>">Edit</a> | 
                    <a href="../actions/menu_delete_action.php?id=<?= $m['menu_id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>