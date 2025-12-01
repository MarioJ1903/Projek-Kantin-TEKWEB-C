<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - E-Kantin</title>
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
        .register-image {
            background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }
        
        .register-image::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8));
        }

        .register-caption {
            position: absolute;
            bottom: 50px;
            left: 50px;
            color: white;
            z-index: 2;
        }

        /* --- BAGIAN KANAN (FORM) --- */
        .register-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px;
            background-color: #ffffff;
        }

        .register-card {
            width: 100%;
            /* UKURAN DISAMAKAN DENGAN LOGIN (450px) */
            max-width: 450px; 
        }

        /* Input Style */
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
        .btn-register {
            background: linear-gradient(to right, #1488cc, #2b32b2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-register:hover {
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

        @media (max-width: 992px) {
            .register-image { display: none; }
            .register-form-container { background: #f8f9fa; }
        }
    </style>
</head>
<body>

    <div class="row g-0">
        <div class="col-lg-7 d-none d-lg-block register-image">
            <div class="register-caption">
                <h1 class="fw-bold display-4">Bergabunglah Bersama Kami</h1>
                <p class="lead">Buat akun sekarang dan nikmati kemudahan memesan makanan.</p>
            </div>
        </div>

        <div class="col-lg-5 register-form-container">
            <div class="register-card">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark">Daftar Akun Baru</h2>
                    <p class="text-muted">Isi data diri Anda dengan benar</p>
                </div>

                <div id="alert-box"></div>

                <form id="registerForm">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Nama Lengkap" required>
                        <label for="name"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>

                    <button type="submit" class="btn btn-register w-100" id="btnRegister">
                        Daftar Sekarang <i class="fas fa-user-plus ms-2"></i>
                    </button>

                    <p class="text-center mt-4 text-muted small">
                        Sudah punya akun? <a href="login.php" class="text-link">Login disini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnRegister');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mendaftar...';
            btn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch('../api/auth_api.php?action=register', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('alert-box').innerHTML = `
                        <div class="alert alert-success shadow-sm border-0 border-start border-5 border-success">
                            <i class="fas fa-check-circle me-2"></i> <b>Sukses!</b> Mengalihkan ke login...
                        </div>`;
                    setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                } else {
                    document.getElementById('alert-box').innerHTML = `
                        <div class="alert alert-danger shadow-sm border-0 border-start border-5 border-danger">
                            <i class="fas fa-times-circle me-2"></i> ${data.message}
                        </div>`;
                    btn.innerHTML = originalText; btn.disabled = false;
                }
            } catch (error) {
                alert("Gagal terhubung ke server.");
                btn.innerHTML = originalText; btn.disabled = false;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>