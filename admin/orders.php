<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../public/login.php"); 
    exit(); 
}
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
        .table thead th { background-color: #212529; color: white; border: none; }
        .btn-detail { background-color: #17a2b8; color: white; border: none; border-radius: 30px; padding: 5px 15px; font-size: 0.85rem; transition: 0.3s; }
        .btn-detail:hover { background-color: #138496; transform: scale(1.05); }

        /* TOMBOL LIHAT WEB LUCU */
        .btn-visit-web {
            background-color: #fff; color: #2b32b2; border-radius: 50px; padding: 8px 25px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 0 rgba(0,0,0,0.1); transition: all 0.2s ease; display: inline-flex; align-items: center; border: 2px solid transparent;
        }
        .btn-visit-web:hover { transform: translateY(-3px); box-shadow: 0 8px 0 rgba(0,0,0,0.1); color: #1488cc; background-color: #f0f8ff; }
        .btn-visit-web:active { transform: translateY(2px); box-shadow: 0 2px 0 rgba(0,0,0,0.1); }
        .btn-visit-web i { transition: transform 0.3s ease; }
        .btn-visit-web:hover i { transform: translateX(3px) rotate(-15deg); }
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
                    <a href="../public/menu.php" class="btn-visit-web me-3" target="_blank">
                        <i class="fas fa-rocket me-2"></i> Lihat Web
                    </a>
                    <a href="../api/auth_api.php?action=logout" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        Logout <i class="fas fa-sign-out-alt ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pb-5">
        <div class="card card-shadow">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Semua Transaksi Masuk</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-striped">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nama Pemesan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-order-list">
                            <tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Rincian Pesanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush" id="modal-items"></ul>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function loadAllOrders() {
            try {
                const res = await fetch('../api/order_api.php?action=admin_history');
                const data = await res.json();
                
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi masuk.</td></tr>';
                } else {
                    data.forEach(o => {
                        let total = new Intl.NumberFormat('id-ID').format(o.total_price);
                        let date = new Date(o.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                        
                        html += `
                        <tr>
                            <td class="ps-4 fw-bold">#${o.order_id}</td>
                            <td>${o.user_name}</td>
                            <td>${date}</td>
                            <td class="fw-bold text-success">Rp ${total}</td>
                            <td><span class="badge bg-success rounded-pill px-3">${o.status.toUpperCase()}</span></td>
                            <td>
                                <button onclick="showDetail(${o.order_id})" class="btn btn-detail shadow-sm">
                                    <i class="fas fa-list me-1"></i> Item
                                </button>
                            </td>
                        </tr>`;
                    });
                }
                document.getElementById('admin-order-list').innerHTML = html;
            } catch (e) { console.error(e); }
        }

        async function showDetail(id) {
            const res = await fetch(`../api/order_api.php?action=details&order_id=${id}`);
            const items = await res.json();
            let html = '';
            items.forEach(i => {
                html += `
                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="fw-bold">${i.name}</div>
                        <small class="text-muted">${i.quantity} x Rp ${new Intl.NumberFormat('id-ID').format(i.price)}</small>
                    </div>
                    <span class="fw-bold text-dark">Rp ${new Intl.NumberFormat('id-ID').format(i.price * i.quantity)}</span>
                </li>`;
            });
            document.getElementById('modal-items').innerHTML = html;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        loadAllOrders();
    </script>
</body>
</html>