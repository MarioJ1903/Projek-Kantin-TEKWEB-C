<!DOCTYPE html>
<html lang="id">
<?php
// 1. Panggil script Auto Login & Cek Session
include "../config/auto_login.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        
        /* Navbar Gradient Biru */
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { font-weight: 500; color: rgba(255,255,255,0.9) !important; transition: 0.3s; padding-bottom: 5px; /* Jarak untuk garis */ }
        
        .nav-link:hover { color: #fff !important; transform: translateY(-2px); text-shadow: 0 2px 4px rgba(0,0,0,0.2); }

        /* --- PERBAIKAN: GARIS BAWAH PADA TAB AKTIF --- */
        .nav-link.active { 
            color: #fff !important; 
            font-weight: 700;
            transform: translateY(-2px); 
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border-bottom: 3px solid #fff; /* Garis Bawah Putih */
        }

        .hero-section { background: white; padding: 50px 0 40px; text-align: center; border-radius: 0 0 50px 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .hero-title { color: #2b32b2; font-weight: 700; }

        /* Card Menu Styles */
        .card-menu { border: none; border-radius: 20px; background: white; transition: all 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); overflow: hidden; height: 100%; position: relative; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .card-img-wrapper { height: 180px; overflow: hidden; position: relative; }
        .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .card-menu:hover .card-img-top { transform: scale(1.1); }
        
        .stock-badge { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; backdrop-filter: blur(4px); z-index: 10; }
        .price-tag { font-size: 1.1rem; font-weight: 700; color: #2b32b2; }
        
        .btn-pesan { background: linear-gradient(to right, #1488cc, #2b32b2); color: white; border: none; width: 100%; padding: 10px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-pesan:hover { opacity: 0.9; transform: scale(1.02); color: white; }
        .btn-pesan:disabled { background: #ccc; cursor: not-allowed; transform: none; }

        /* Modal Styles */
        .modal-content { border-radius: 20px; border: none; overflow: hidden; }
        .modal-header { background: linear-gradient(to right, #2b32b2, #1488cc); color: white; border: none; }
        .modal-img { width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 15px; }
        
        .qty-control { display: flex; align-items: center; justify-content: center; gap: 10px; margin: 20px 0; }
        .btn-qty { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #eee; background: white; font-weight: bold; color: #2b32b2; transition: 0.2s; }
        .btn-qty:hover { background: #2b32b2; color: white; border-color: #2b32b2; }
        .input-qty-modal { width: 60px; text-align: center; border: none; font-size: 1.2rem; font-weight: bold; color: #333; outline: none; }
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
                        <a class="nav-link position-relative btn btn-sm btn-outline-light ms-3 px-3 border-0" href="cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-white-50" href="../api/auth_api.php?action=logout">Logout <i class="fas fa-sign-out-alt ms-1"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title display-5">Mau Makan Apa Hari Ini?</h1>
            <p class="text-muted lead">Pilih menu favoritmu dan pesan sekarang.</p>
        </div>
    </section>

    <div class="container pb-5">
        <div class="row g-4" id="menu-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Sedang mengambil daftar menu...</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalMenuName">Nama Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img src="" id="modalMenuImg" class="modal-img shadow-sm">
                    
                    <h4 class="text-primary fw-bold mb-1" id="modalMenuPrice">Rp 0</h4>
                    <p class="text-muted small mb-3">Stok Tersedia: <span id="modalMenuStock" class="fw-bold text-dark">0</span></p>
                    
                    <div class="qty-control">
                        <button type="button" class="btn-qty" onclick="changeQty(-1)"><i class="fas fa-minus"></i></button>
                        <input type="number" id="modalQty" class="input-qty-modal" value="1" min="1" readonly>
                        <button type="button" class="btn-qty" onclick="changeQty(1)"><i class="fas fa-plus"></i></button>
                    </div>

                    <input type="hidden" id="modalMenuId">
                    <button onclick="confirmAddToCart()" class="btn btn-pesan w-100 py-3 mt-2 shadow">
                        <i class="fas fa-cart-plus me-2"></i> Masukkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Variabel global untuk menyimpan data stok menu yang sedang dibuka
        let currentStock = 0;

        // 1. Fungsi Load Menu dari API
        async function loadMenu() {
            try {
                const response = await fetch('../api/menu_api.php?action=read');
                const data = await response.json();
                let html = '';
                
                if(data.length === 0) {
                    document.getElementById('menu-container').innerHTML = '<div class="col-12 text-center text-muted"><h5>Belum ada menu tersedia.</h5></div>';
                    return;
                }

                data.forEach(item => {
                    let price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price);
                    let imgSrc = item.image && item.image !== 'default.jpg' 
                        ? `../assets/img/${item.image}` 
                        : `https://placehold.co/600x400/orange/white?text=${encodeURIComponent(item.name)}`; 
                    
                    let isHabis = item.stock == 0;
                    let badgeClass = isHabis ? 'bg-secondary' : (item.stock < 5 ? 'bg-danger' : 'bg-success');
                    let badgeText = isHabis ? 'Habis' : `Stok: ${item.stock}`;
                    let btnText = isHabis ? 'Stok Habis' : 'Pesan';
                    let btnState = isHabis ? 'disabled' : '';

                    // Siapkan data untuk dikirim ke Modal saat tombol diklik
                    let menuData = JSON.stringify({
                        id: item.menu_id,
                        name: item.name,
                        price: item.price,
                        stock: item.stock,
                        image: imgSrc
                    }).replace(/"/g, '&quot;');

                    html += `
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card card-menu h-100">
                            <div class="card-img-wrapper">
                                <span class="stock-badge ${badgeClass}">${badgeText}</span>
                                <img src="${imgSrc}" class="card-img-top" alt="${item.name}">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="fw-bold mb-1 text-truncate">${item.name}</h5>
                                <div class="mt-auto">
                                    <p class="price-tag mb-3">${price}</p>
                                    <button onclick="openOrderModal(${menuData})" class="btn btn-pesan shadow-sm" ${btnState}>
                                        <i class="fas fa-plus-circle me-1"></i> ${btnText}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });

                document.getElementById('menu-container').innerHTML = html;
                updateCartBadge(); // Update ikon keranjang

            } catch (error) { console.error('Error:', error); }
        }

        // 2. Fungsi Buka Modal
        function openOrderModal(menu) {
            // Isi data ke dalam Modal
            document.getElementById('modalMenuId').value = menu.id;
            document.getElementById('modalMenuName').innerText = menu.name;
            document.getElementById('modalMenuPrice').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(menu.price);
            document.getElementById('modalMenuImg').src = menu.image;
            document.getElementById('modalMenuStock').innerText = menu.stock;
            
            // Reset qty ke 1
            document.getElementById('modalQty').value = 1;
            currentStock = menu.stock;

            // Tampilkan Modal
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }

        // 3. Fungsi Ubah Jumlah (+/-)
        function changeQty(amount) {
            let input = document.getElementById('modalQty');
            let newVal = parseInt(input.value) + amount;
            
            // Validasi: Tidak boleh < 1 dan tidak boleh > Stok
            if (newVal >= 1 && newVal <= currentStock) {
                input.value = newVal;
            }
        }

        // 4. Fungsi Kirim ke Keranjang (API)
        async function confirmAddToCart() {
            let id = document.getElementById('modalMenuId').value;
            let qty = parseInt(document.getElementById('modalQty').value);

            try {
                const response = await fetch('../api/cart_api.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ menu_id: id, quantity: qty })
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    // Tutup Modal
                    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                    updateCartBadge();
                    alert(`✅ Berhasil menambahkan ${qty} item ke keranjang!`);
                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) { console.error('Error:', error); }
        }

        // 5. Fungsi Update Badge Keranjang
        async function updateCartBadge() {
            try {
                const response = await fetch('../api/cart_api.php?action=read');
                const data = await response.json();
                let totalItems = 0;
                if(Array.isArray(data)) {
                    data.forEach(item => totalItems += item.quantity);
                }
                document.getElementById('cart-count').innerText = totalItems;
            } catch (e) { }
        }

        document.addEventListener("DOMContentLoaded", loadMenu);
    </script>
</body>
</html>