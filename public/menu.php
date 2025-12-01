<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(to right, #2b32b2, #1488cc);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Hero Section */
        .hero-section {
            background: white;
            padding: 60px 0 40px;
            text-align: center;
            border-radius: 0 0 50px 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        .hero-title {
            color: #2b32b2;
            font-weight: 700;
        }

        /* Card Menu Styling */
        .card-menu {
            border: none;
            border-radius: 20px;
            background: white;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            height: 100%;
        }
        .card-menu:hover {
            transform: translateY(-10px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.1);
        }
        .card-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .card-menu:hover .card-img-top {
            transform: scale(1.1);
        }
        
        /* Badge Stok */
        .badge-stock {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Harga & Button */
        .price-tag {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2b32b2;
        }
        .btn-order {
            background: linear-gradient(to right, #1488cc, #2b32b2);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }
        .btn-order:hover {
            opacity: 0.9;
            transform: scale(1.02);
            color: white;
        }
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
                    <li class="nav-item">
                        <a class="nav-link position-relative btn btn-sm btn-outline-light ms-3 px-3 border-0" href="cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                0
                            </span>
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
            <p class="text-muted lead">Pesan makanan favoritmu tanpa perlu antri panjang.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fungsi Memuat Menu
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
                    // Format Harga Rupiah
                    let price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price);
                    
                    // Logika Gambar (Gunakan Placeholder jika kosong)
                    // URL ini akan menghasilkan gambar makanan acak yang cantik
                    let imgSrc = item.image && item.image !== 'default.jpg' 
                        ? `../assets/img/${item.image}` 
                        : `https://placehold.co/600x400/orange/white?text=${encodeURIComponent(item.name)}`; 
                    
                    // Logika Stok
                    let stockBadge = '';
                    if(item.stock == 0) {
                        stockBadge = '<span class="badge bg-secondary badge-stock">Habis</span>';
                    } else if(item.stock < 5) {
                        stockBadge = `<span class="badge bg-danger badge-stock">Sisa ${item.stock}</span>`;
                    } else {
                        stockBadge = `<span class="badge bg-success badge-stock">Ready</span>`;
                    }

                    // Tombol Disable jika stok habis
                    let btnStatus = item.stock == 0 ? 'disabled' : '';
                    let btnText = item.stock == 0 ? 'Stok Habis' : '<i class="fas fa-plus-circle me-1"></i> Tambah Pesanan';

                    html += `
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card card-menu h-100">
                            <div class="card-img-wrapper">
                                ${stockBadge}
                                <img src="${imgSrc}" class="card-img-top" alt="${item.name}">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title fw-bold mb-1">${item.name}</h5>
                                <p class="text-muted small mb-3">Kategori: Makanan</p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="price-tag">${price}</span>
                                        <small class="text-muted"><i class="fas fa-box"></i> Stok: ${item.stock}</small>
                                    </div>
                                    <button onclick="addToCart(${item.menu_id})" class="btn btn-order shadow-sm" ${btnStatus}>
                                        ${btnText}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });

                document.getElementById('menu-container').innerHTML = html;
                updateCartBadge(); // Update badge saat load

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('menu-container').innerHTML = '<div class="col-12 text-center text-danger">Gagal memuat data menu.</div>';
            }
        }

        // Fungsi Tambah Keranjang
        async function addToCart(id) {
            try {
                const response = await fetch('../api/cart_api.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ menu_id: id })
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    // Animasi Badge Berubah
                    updateCartBadge();
                    alert("✅ Berhasil masuk keranjang!");
                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Fungsi Update Badge Keranjang (Memanggil API Cart)
        async function updateCartBadge() {
            try {
                const response = await fetch('../api/cart_api.php?action=read');
                const data = await response.json();
                let totalItems = 0;
                
                // Hitung total item
                if(Array.isArray(data)) {
                    data.forEach(item => totalItems += item.quantity);
                }
                
                document.getElementById('cart-count').innerText = totalItems;
            } catch (e) {
                console.log("Gagal load cart count");
            }
        }

        // Jalankan saat halaman siap
        document.addEventListener("DOMContentLoaded", loadMenu);
    </script>
</body>
</html>