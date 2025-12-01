<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 400; }
        .nav-link:hover { color: #fff !important; transform: translateY(-1px); }
        .nav-link.active { font-weight: 700 !important; color: #fff !important; } /* BOLD ACTIVE */

        .card-cart { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .table thead th { background-color: #f8f9fa; border-bottom: 2px solid #eef2f7; color: #2b32b2; font-weight: 600; }
        .btn-checkout { background: linear-gradient(to right, #11998e, #38ef7d); border: none; color: white; padding: 12px 30px; border-radius: 30px; font-weight: 600; transition: 0.3s; box-shadow: 0 4px 15px rgba(56, 239, 125, 0.3); }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(56, 239, 125, 0.4); color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="menu.php"><i class="fas fa-utensils me-2"></i>E-Kantin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="menu.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Riwayat</a></li>
                    <li class="nav-item">
                        <a class="nav-link active position-relative btn btn-sm ms-2 px-3" href="#">
                            <i class="fas fa-shopping-cart fa-lg"></i> Keranjang
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-warning" href="../api/auth_api.php?action=logout">Logout <i class="fas fa-sign-out-alt ms-1"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pb-5">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white p-3 rounded-circle shadow-sm me-3 text-primary"><i class="fas fa-shopping-cart fa-2x"></i></div>
            <div><h3 class="fw-bold mb-0 text-dark">Keranjang Belanja</h3></div>
        </div>
        <div class="card card-cart p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Menu</th><th>Harga</th><th class="text-center">Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody id="cart-list"><tr><td colspan="4" class="text-center py-5">Memuat...</td></tr></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <h3 class="fw-bold text-primary" id="total-price">Rp 0</h3>
                <button onclick="showPaymentModal()" class="btn btn-checkout">Bayar Sekarang</button>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="paymentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg"><div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold">Scan Pembayaran</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body p-4 text-center"><img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" width="150"><h4 class="mt-3 text-primary fw-bold" id="modal-total-price">Rp 0</h4><div class="alert alert-info mt-3 small">Ini simulasi pembayaran.</div></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button onclick="processCheckout()" class="btn btn-success fw-bold">Saya Sudah Bayar</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentTotal = 0;
        async function loadCart() {
            try {
                const res = await fetch('../api/cart_api.php?action=read'); const data = await res.json();
                let html = ''; currentTotal = 0;
                if(data.length===0){ document.getElementById('cart-list').innerHTML='<tr><td colspan="4" class="text-center py-5">Keranjang kosong.</td></tr>'; document.getElementById('total-price').innerText='Rp 0'; return; }
                data.forEach(item => {
                    let sub = item.price*item.quantity; currentTotal+=sub;
                    html+=`<tr><td><div class="fw-bold">${item.name}</div></td><td>Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td><td class="text-center"><span class="badge bg-light text-dark border px-3">${item.quantity}</span></td><td class="text-end fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(sub)}</td></tr>`;
                });
                document.getElementById('cart-list').innerHTML=html; document.getElementById('total-price').innerText='Rp '+new Intl.NumberFormat('id-ID').format(currentTotal);
            } catch(e){}
        }
        function showPaymentModal(){ if(currentTotal===0) return alert("Kosong!"); document.getElementById('modal-total-price').innerText='Rp '+new Intl.NumberFormat('id-ID').format(currentTotal); new bootstrap.Modal(document.getElementById('paymentModal')).show(); }
        async function processCheckout(){ 
            try { const res=await fetch('../api/order_api.php?action=checkout'); const data=await res.json(); 
            if(data.status){ alert('Berhasil!'); window.location.href='orders.php'; } else { alert(data.message); } } catch(e){} 
        }
        loadCart();
    </script>
</body>
</html>