<!DOCTYPE html>
<html>
<head>
    <title>Login Kantin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <h2>Login</h2>
        <?php if(isset($_GET['error'])) echo "<div class='alert'>Login Gagal! Cek email/password.</div>"; ?>
        <form action="../actions/login_action.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>
</body>
</html>