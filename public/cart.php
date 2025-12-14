<!DOCTYPE html>
<html lang="id">
<?php
// 1. Cek Login
include "../config/auto_login.php";
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
?>
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        
        /* NAVBAR FIX */
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { font-weight: 500; color: rgba(255,255,255,0.9) !important; transition: 0.3s; padding-bottom: 5px; /* Jarak untuk garis */ }
        
        
        /* Efek Hover & Active */
        .nav-link:hover { 
            color: #fff !important; 
            transform: translateY(-2px); 
        }

        /* TAB KERANJANG AKTIF */
        .cart-active {
            font-weight: 700 !important;
            color: #fff !important;
            border-bottom: 3px solid #fff; /* GARIS BAWAH PUTIH */
            display: flex;
            align-items: center;
            gap: 8px; /* Jarak antara ikon dan tulisan */
        }

        /* CONTENT STYLES */
        .card-cart { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .table thead th { background-color: #f8f9fa; border-bottom: 2px solid #eef2f7; color: #105dc3; font-weight: 600; }
        
        /* Tombol Checkout */
        .btn-checkout { 
            background-color: #1dd1a1; 
            border: none; color: white; padding: 12px 30px; 
            border-radius: 30px; font-weight: 600; transition: 0.3s; 
            box-shadow: 0 4px 15px rgba(29, 209, 161, 0.3); 
        }
        .btn-checkout:hover { transform: translateY(-2px); background-color: #10ac84; color: white; }

        /* Tombol Qty */
        .btn-qty-cart { 
            width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd; 
            background: white; color: #105dc3; font-weight: bold; 
            display: inline-flex; align-items: center; justify-content: center; cursor: pointer; 
        }
        .btn-qty-cart:hover { background: #105dc3; color: white; border-color: #105dc3; }
        .qty-text { font-weight: 600; margin: 0 10px; min-width: 20px; text-align: center; }
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
                    
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Beranda</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link cart-active" href="#">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span>Keranjang</span> </a>
                    </li>

                    <li class="nav-item ms-3">
                        <a class="nav-link text-white-50 small fw-bold" href="../api/auth_api.php?action=logout">
                            Logout <i class="fas fa-sign-out-alt ms-1"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pb-5">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white p-3 rounded-circle shadow-sm me-3 text-primary">
                <i class="fas fa-shopping-cart fa-2x"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-dark">Keranjang Belanja</h3>
            </div>
        </div>
        
        <div class="card card-cart p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="cart-list">
                        <tr><td colspan="4" class="text-center py-5">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
            
            <hr>

            <div class="d-flex justify-content-between align-items-center mt-4">
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
                const res = await fetch('../api/cart_api.php?action=read'); 
                const data = await res.json();
                
                let html = ''; 
                currentTotal = 0; 

                if(data.length === 0){ 
                    document.getElementById('cart-list').innerHTML='<tr><td colspan="4" class="text-center py-5 text-muted">Keranjang kosong. <a href="menu.php">Belanja Yuk!</a></td></tr>'; 
                    document.getElementById('total-price').innerText='Rp 0'; 
                    return; 
                }

                data.forEach(item => {
                    let sub = item.price * item.quantity;
                    currentTotal += sub;
                    
                    html += `
                    <tr>
                        <td>
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">Stok: ${item.stock}</small>
                        </td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn-qty-cart" onclick="updateQty(${item.menu_id}, ${item.quantity}, -1, ${item.stock})"><i class="fas fa-minus"></i></button>
                                <span class="qty-text">${item.quantity}</span>
                                <button class="btn-qty-cart" onclick="updateQty(${item.menu_id}, ${item.quantity}, 1, ${item.stock})"><i class="fas fa-plus"></i></button>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(sub)}</td>
                    </tr>`;
                });

                document.getElementById('cart-list').innerHTML = html;
                document.getElementById('total-price').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentTotal);

            } catch(e) { console.error(e); }
        }

        async function updateQty(menuId, currentQty, change, maxStock) {
            let newQty = currentQty + change;
            if (change > 0 && newQty > maxStock) { alert("Stok tidak mencukupi!"); return; }
            if (newQty === 0) { if(!confirm("Hapus item ini dari keranjang?")) return; }

            try {
                const response = await fetch('../api/cart_api.php?action=update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ menu_id: menuId, quantity: newQty })
                });
                const result = await response.json();
                if(result.status === 'success') loadCart();
            } catch(e) {}
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