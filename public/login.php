<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
        }

        /* --- BAGIAN KIRI (GAMBAR) --- */
        .login-image {
            background: url('https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }
        
        .login-image::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
        }

        .login-caption {
            position: absolute;
            bottom: 50px;
            left: 50px;
            color: white;
            z-index: 2;
        }

        /* --- BAGIAN KANAN (FORM) --- */
        .login-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px;
            background-color: #ffffff;
        }

        .login-card {
            width: 100%;
            /* DISAMAKAN DENGAN REGISTER */
            max-width: 450px; 
        }

        /* Input Styling */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #2b32b2;
            font-weight: 600;
        }
        
        .form-control:focus {
            border-color: #2b32b2;
            box-shadow: 0 0 0 0.25rem rgba(43, 50, 178, 0.25);
        }

        /* Tombol */
        .btn-login {
            background: linear-gradient(to right, #1488cc, #2b32b2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(20, 136, 204, 0.3);
            color: white;
        }
        
        /* Link Biru */
        a.text-link {
            color: #2b32b2;
            text-decoration: none;
            font-weight: 600;
        }
        a.text-link:hover {
            text-decoration: underline;
        }

        /* Responsif */
        @media (max-width: 992px) {
            .login-image { display: none; }
            .login-form-container { background: #f8f9fa; }
        }
    </style>
</head>
<body>

    <div class="row g-0">
        <div class="col-lg-7 d-none d-lg-block login-image">
            <div class="login-caption">
                <h1 class="fw-bold display-4">Selamat Datang</h1>
                <p class="lead">Nikmati kemudahan memesan makanan di E-Kantin.</p>
            </div>
        </div>

        <div class="col-lg-5 login-form-container">
            <div class="login-card">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-dark">Login Akun</h2>
                    <p class="text-muted">Silakan masuk untuk mulai memesan</p>
                </div>

                <div id="alert-box"></div>

                <form id="loginForm">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-muted small" for="rememberMe">Ingat Saya</label>
                        </div>
                        <a href="forgot_password.php" class="text-decoration-none small text-primary fw-bold">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn btn-login w-100" id="btnLogin">
                        Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    <p class="text-center mt-4 text-muted small">
                        Belum punya akun? <a href="register.php" class="text-link">Daftar disini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Loading...';
            btn.disabled = true;

            const formData = new FormData(this);
            formData.append('remember', document.getElementById('rememberMe').checked);
            
            try {
                const response = await fetch('../api/auth_api.php?action=login', { method: 'POST', body: formData });
                const text = await response.text();
                try {
                    const data = JSON.parse(text);
                    if(data.status === 'success') {
                        window.location.href = data.role === 'admin' ? '../admin/index.php' : 'menu.php';
                    } else {
                        showError(data.message);
                    }
                } catch (err) { showError("Terjadi kesalahan server."); }
            } catch (error) { showError("Gagal terhubung ke server."); } 
            finally { btn.innerHTML = originalText; btn.disabled = false; }
        });

        function showError(msg) {
            document.getElementById('alert-box').innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-5 border-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>