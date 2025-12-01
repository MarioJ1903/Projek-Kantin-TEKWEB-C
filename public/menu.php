<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        
        /* Navbar Konsisten */
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; transition: 0.3s; font-weight: 400; }
        .nav-link:hover { color: #fff !important; transform: translateY(-1px); }
        
        /* Active State (BOLD) */
        .nav-link.active { 
            font-weight: 700 !important; 
            color: #fff !important; 
            border-bottom: 2px solid rgba(255,255,255,0.5); 
        }

        /* Hero & Cards */
        .hero-section { background: white; padding: 60px 0 40px; border-radius: 0 0 50px 50px; margin-bottom: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); text-align: center; }
        .hero-title { color: #2b32b2; font-weight: 700; }
        .card-menu { border: none; border-radius: 20px; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.05); overflow: hidden; height: 100%; transition: 0.3s; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .card-img-wrapper { height: 200px; overflow: hidden; position: relative; }
        .card-img-top { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-order { background: linear-gradient(to right, #1488cc, #2b32b2); border: none; color: white; border-radius: 12px; padding: 8px; width: 100%; font-weight: 600; }
        .btn-order:hover { opacity: 0.9; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-utensils me-2"></i>E-Kantin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="menu.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Riwayat</a></li>
                    <li class="nav-item">
                        <a class="nav-link position-relative btn btn-sm ms-2 px-3" href="cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-warning" href="../api/auth_api.php?action=logout">Logout <i class="fas fa-sign-out-alt ms-1"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title display-5">Mau Makan Apa Hari Ini?</h1>
            <p class="text-muted lead">Pesan makanan favoritmu tanpa perlu antri panjang.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="row g-4" id="menu-container">
            <div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ... (Script JS Load Menu & Cart SAMA SEPERTI SEBELUMNYA) ...
        async function loadMenu() {
            try {
                const res = await fetch('../api/menu_api.php?action=read');
                const data = await res.json();
                let html = '';
                if(data.length === 0) { document.getElementById('menu-container').innerHTML = '<div class="col-12 text-center">Belum ada menu.</div>'; return; }
                
                data.forEach(item => {
                    let price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price);
                    let imgSrc = item.image && item.image !== 'default.jpg' ? `../assets/img/${item.image}` : `https://placehold.co/600x400/orange/white?text=${item.name}`;
                    let btnStatus = item.stock == 0 ? 'disabled' : '';
                    let btnText = item.stock == 0 ? 'Habis' : 'Pesan';
                    let badge = item.stock == 0 ? '<span class="badge bg-secondary position-absolute m-2">Habis</span>' : '<span class="badge bg-success position-absolute m-2">Ready</span>';

                    html += `
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card card-menu h-100">
                            <div class="card-img-wrapper">
                                ${badge}
                                <img src="${imgSrc}" class="card-img-top" alt="${item.name}">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h6 class="fw-bold mb-1">${item.name}</h6>
                                <div class="mt-auto">
                                    <p class="text-primary fw-bold mb-2">${price}</p>
                                    <button onclick="addToCart(${item.menu_id})" class="btn btn-order shadow-sm" ${btnStatus}>
                                        <i class="fas fa-plus-circle"></i> ${btnText}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                document.getElementById('menu-container').innerHTML = html;
                updateCartBadge();
            } catch (e) { console.error(e); }
        }
        async function addToCart(id) {
            await fetch('../api/cart_api.php?action=add', { method: 'POST', body: JSON.stringify({ menu_id: id }) });
            updateCartBadge(); alert("Masuk Keranjang!");
        }
        async function updateCartBadge() {
            const res = await fetch('../api/cart_api.php?action=read');
            const data = await res.json();
            let t = 0; if(Array.isArray(data)) data.forEach(i => t += i.quantity);
            document.getElementById('cart-count').innerText = t;
        }
        document.addEventListener("DOMContentLoaded", loadMenu);
    </script>
</body>
</html>