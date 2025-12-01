<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - E-Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9; /* Background abu-abu muda yang bersih */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forgot-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            border: none;
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: #eef2ff;
            color: #2b32b2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }

        /* Input Style Modern */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #2b32b2;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #2b32b2;
            box-shadow: 0 0 0 0.25rem rgba(43, 50, 178, 0.25);
        }

        /* Tombol Gradient */
        .btn-primary-gradient {
            background: linear-gradient(to right, #1488cc, #2b32b2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(20, 136, 204, 0.3);
            color: white;
        }

        a.text-link {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        a.text-link:hover {
            color: #2b32b2;
        }
    </style>
</head>
<body>

    <div class="forgot-card text-center">
        <div class="icon-wrapper">
            <i class="fas fa-key"></i>
        </div>

        <h3 class="fw-bold text-dark mb-2">Lupa Password?</h3>
        <p class="text-muted mb-4 small">Jangan khawatir! Masukkan email yang terdaftar dan kami akan mengirimkan link reset.</p>

        <form onsubmit="return handleReset(event)">
            <div class="form-floating mb-4 text-start">
                <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
            </div>

            <button type="submit" class="btn btn-primary-gradient w-100 mb-3">
                Kirim Link Reset
            </button>

            <div>
                <a href="login.php" class="text-link small">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>

    <script>
        function handleReset(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            // Simulasi Reset (Karena butuh SMTP Server asli untuk email)
            alert(`Link reset password telah dikirim ke: ${email}\n(Ini hanya simulasi UI)`);
            window.location.href = 'login.php';
        }
    </script>

</body>
</html>