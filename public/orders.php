<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 400; }
        .nav-link:hover { color: #fff !important; transform: translateY(-1px); }
        
        /* Active State Bold */
        .nav-link.active { font-weight: 700 !important; color: #fff !important; border-bottom: 2px solid rgba(255,255,255,0.5); }

        .card-history { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .table thead th { background-color: #f8f9fa; border-bottom: 2px solid #eef2f7; color: #2b32b2; font-weight: 600; }
        .btn-detail { background-color: #eef2ff; color: #2b32b2; border-radius: 20px; font-weight: 600; padding: 5px 15px; border:none; transition:0.3s; }
        .btn-detail:hover { background-color: #2b32b2; color: white; }
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
                    <li class="nav-item"><a class="nav-link active" href="orders.php">Riwayat</a></li>
                    <li class="nav-item">
                        <a class="nav-link position-relative btn btn-sm ms-2 px-3" href="cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-warning" href="../api/auth_api.php?action=logout">Logout <i class="fas fa-sign-out-alt ms-1"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white p-3 rounded-circle shadow-sm me-3 text-primary"><i class="fas fa-history fa-2x"></i></div>
            <div>
                <h3 class="fw-bold mb-0 text-dark">Riwayat Pesanan</h3>
                <p class="text-muted mb-0">Daftar transaksi Anda</p>
            </div>
        </div>
        
        <div class="card card-history p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID Order</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="order-list"><tr><td colspan="5" class="text-center py-5">Memuat...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold">Rincian</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><ul class="list-group list-group-flush" id="modal-items"></ul></div><div class="modal-footer bg-light"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button></div></div></div></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function loadHistory() {
            try {
                const res = await fetch('../api/order_api.php?action=history'); const data = await res.json();
                let html = '';
                if(data.length===0){ html='<tr><td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat.</td></tr>'; }
                else { data.forEach(o => {
                    let total=new Intl.NumberFormat('id-ID').format(o.total_price);
                    let date=new Date(o.created_at).toLocaleString('id-ID');
                    let statusBadge=o.status==='selesai'?'bg-success':'bg-warning text-dark';
                    html+=`<tr><td class="fw-bold text-muted">#${o.order_id}</td><td>${date}</td><td class="fw-bold text-primary">Rp ${total}</td><td><span class="badge ${statusBadge} rounded-pill px-3">${o.status}</span></td><td><button onclick="showDetail(${o.order_id})" class="btn btn-detail shadow-sm">Detail</button></td></tr>`;
                }); }
                document.getElementById('order-list').innerHTML = html;
            } catch(e){}
        }
        async function showDetail(id) {
            const res=await fetch(`../api/order_api.php?action=details&order_id=${id}`); const items=await res.json();
            let html=''; items.forEach(i=>{ let sub=new Intl.NumberFormat('id-ID').format(i.price*i.quantity); html+=`<li class="list-group-item d-flex justify-content-between align-items-center p-3"><div><div class="fw-bold">${i.name}</div><small class="text-muted">${i.quantity} x ${i.price}</small></div><span class="fw-bold">Rp ${sub}</span></li>`; });
            document.getElementById('modal-items').innerHTML=html; new bootstrap.Modal(document.getElementById('detailModal')).show();
        }
        loadHistory();
    </script>
</body>
</html>