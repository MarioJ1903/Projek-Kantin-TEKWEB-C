<!DOCTYPE html>
<html>
<head>
    <title>Login - E-Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container" style="max-width:400px">
        <div class="card shadow p-4">
            <h3 class="text-center mb-3">Login</h3>
            <div id="alert-box"></div>
            <form id="loginForm">
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
            <p class="mt-3 text-center">Belum punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('../api/auth_api.php?action=login', { method: 'POST', body: formData });
            const data = await res.json();
            
            if(data.status === 'success') {
                window.location.href = data.role === 'admin' ? '../admin/index.php' : 'menu.php';
            } else {
                document.getElementById('alert-box').innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        });
    </script>
</body>
</html>