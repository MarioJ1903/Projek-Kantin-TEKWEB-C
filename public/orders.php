<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Riwayat Pesanan</h2>
        <a href="menu.php">&laquo; Kembali ke Menu</a>
        <?php while ($o = mysqli_fetch_assoc($query)) { ?>
        <div class="menu-item" style="background:#f9f9f9; padding:10px; margin-top:10px;">
            <b>Order #<?= $o['order_id'] ?></b> - <?= $o['created_at'] ?><br>
            Status: <b><?= $o['status'] ?></b><br>
            Total: Rp <?= number_format($o['total_price']) ?>
        </div>
        <?php } ?>
    </div>
</body>
</html>