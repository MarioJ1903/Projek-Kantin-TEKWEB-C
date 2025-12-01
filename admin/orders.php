<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../public/login.php"); exit(); }
?>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .navbar { background: linear-gradient(to right, #2b32b2, #1488cc); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 400; transition: 0.3s; }
        .nav-link:hover { color: #fff !important; transform: translateY(-1px); }
        .nav-link.active { font-weight: 700 !important; color: #fff !important; border-bottom: 2px solid rgba(255,255,255,0.5); }
        
        .card-shadow { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Kelola Menu</a></li>
                    <li class="nav-item"><a class="nav-link active" href="orders.php">Laporan Transaksi</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="../public/menu.php" class="btn btn-outline-light btn-sm me-3" target="_blank">Lihat Web</a>
                    <a href="../api/auth_api.php?action=logout" class="btn btn-danger btn-sm rounded-pill px-3">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold text-primary">Semua Transaksi</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-striped">
                        <thead><tr><th class="ps-4">ID</th><th>Pemesan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody id="admin-order-list"><tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Detail</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><ul class="list-group" id="modal-items"></ul></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function loadAllOrders() {
            try { const res=await fetch('../api/order_api.php?action=admin_history'); const data=await res.json(); let html=''; 
            data.forEach(o=>{ let t=new Intl.NumberFormat('id-ID').format(o.total_price); let d=new Date(o.created_at).toLocaleString('id-ID'); html+=`<tr><td class="ps-4 fw-bold">#${o.order_id}</td><td>${o.user_name}</td><td>${d}</td><td class="text-success fw-bold">Rp ${t}</td><td><span class="badge bg-success">${o.status}</span></td><td><button class="btn btn-info btn-sm text-white" onclick="showDetail(${o.order_id})">Item</button></td></tr>`; });
            document.getElementById('admin-order-list').innerHTML=html; } catch(e){}
        }
        async function showDetail(id){ const res=await fetch(`../api/order_api.php?action=details&order_id=${id}`); const i=await res.json(); let h=''; i.forEach(x=>{ h+=`<li class="list-group-item d-flex justify-content-between"><span>${x.name} (x${x.quantity})</span><b>Rp ${x.price*x.quantity}</b></li>`}); document.getElementById('modal-items').innerHTML=h; new bootstrap.Modal(document.getElementById('detailModal')).show(); }
        loadAllOrders();
    </script>
</body>
</html>