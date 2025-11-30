<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT c.*, m.name, m.price FROM cart c JOIN menu m ON c.menu_id = m.menu_id WHERE c.user_id = '$user_id'");
$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Keranjang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Keranjang Belanja</h2>
        <a href="menu.php">&laquo; Kembali ke Menu</a>
        <table>
            <tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
            <?php while ($row = mysqli_fetch_assoc($query)) { 
                $sub = $row['price'] * $row['quantity'];
                $total += $sub;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>Rp <?= number_format($sub) ?></td>
            </tr>
            <?php } ?>
        </table>
        <h3>Total: Rp <?= number_format($total) ?></h3>
        <?php if($total > 0): ?>
        <form action="../actions/checkout_action.php" method="POST">
            <button type="submit" onclick="return confirm('Bayar sekarang?')">Checkout & Bayar</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>