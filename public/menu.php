<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$query = mysqli_query($conn, "SELECT * FROM menu WHERE stock > 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu Kantin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Daftar Menu</h2>
        <nav>
            <a href="cart.php">Keranjang</a> | 
            <a href="orders.php">Riwayat Pesanan</a> | 
            <a href="../actions/logout.php" style="color:red;">Logout</a>
        </nav>
        <hr>
        <?php while ($m = mysqli_fetch_assoc($query)) { ?>
        <div class="menu-item">
            <h3><?= htmlspecialchars($m['name']) ?></h3>
            <p>Harga: Rp <?= number_format($m['price']) ?> | Stok: <?= $m['stock'] ?></p>
            <form action="../actions/cart_add.php" method="POST">
                <input type="hidden" name="menu_id" value="<?= $m['menu_id'] ?>">
                <button type="submit" style="width:auto;">Tambah ke Keranjang</button>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>