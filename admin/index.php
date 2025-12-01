<!DOCTYPE html>
<html lang="id">
<?php
include "../config/auto_login.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../public/login.php"); exit(); }
?>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Kelola Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Laporan Transaksi</a></li>
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
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">Daftar Menu Makanan</h5>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-1"></i> Tambah Menu
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Gambar</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-menu-list"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAddMenu">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Menu Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Nama Menu</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label>Harga (Rp)</label><input type="number" name="price" class="form-control" required></div>
                        <div class="mb-3"><label>Stok Awal</label><input type="number" name="stock" class="form-control" required></div>
                        <div class="mb-3"><label>Foto Menu</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditMenu">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="menu_id" id="edit_menu_id">
                        <div class="mb-3"><label>Nama Menu</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                        <div class="mb-3"><label>Harga (Rp)</label><input type="number" name="price" id="edit_price" class="form-control" required></div>
                        <div class="mb-3"><label>Stok</label><input type="number" name="stock" id="edit_stock" class="form-control" required></div>
                        <div class="mb-3">
                            <label>Ganti Foto (Opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allMenus = []; // Simpan data menu di sini

        async function loadAdminMenu() {
            try {
                const res = await fetch('../api/menu_api.php?action=read');
                const data = await res.json();
                allMenus = data; // Simpan ke variabel global
                
                let html = '';
                data.forEach((item, index) => {
                    let imgSrc = item.image && item.image !== 'default.jpg' ? `../assets/img/${item.image}` : `https://placehold.co/100x100?text=${item.name.substring(0,3)}`;
                    html += `
                    <tr>
                        <td class="ps-4"><img src="${imgSrc}" class="rounded shadow-sm" width="50" height="50" style="object-fit:cover"></td>
                        <td class="fw-bold">${item.name}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                        <td><span class="badge bg-info text-dark">${item.stock}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning text-dark rounded-circle me-1" onclick="openEditModal(${index})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger rounded-circle" onclick="deleteMenu(${item.menu_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                document.getElementById('admin-menu-list').innerHTML = html;
            } catch (error) { console.error(error); }
        }

        // --- FUNGSI BUKA MODAL EDIT ---
        function openEditModal(index) {
            const item = allMenus[index];
            document.getElementById('edit_menu_id').value = item.menu_id;
            document.getElementById('edit_name').value = item.name;
            document.getElementById('edit_price').value = item.price;
            document.getElementById('edit_stock').value = item.stock;
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        // --- PROSES UPDATE MENU ---
        document.getElementById('formEditMenu').addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const res = await fetch('../api/menu_api.php?action=update', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    loadAdminMenu();
                    alert("Menu berhasil diupdate!");
                } else { alert("Gagal update: " + result.message); }
            } catch (error) { alert("Kesalahan sistem."); }
        });

        // --- PROSES TAMBAH MENU ---
        document.getElementById('formAddMenu').addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const res = await fetch('../api/menu_api.php?action=create', { method: 'POST', body: formData });
                const result = await res.json();
                if(result.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                    this.reset();
                    loadAdminMenu();
                    alert("Menu berhasil ditambahkan!");
                } else { alert("Gagal: " + result.message); }
            } catch (error) { alert("Kesalahan sistem."); }
        });

        async function deleteMenu(id) {
            if(!confirm('Hapus menu ini?')) return;
            const formData = new FormData(); formData.append('id', id);
            await fetch('../api/menu_api.php?action=delete', { method: 'POST', body: formData });
            loadAdminMenu();
        }
        
        loadAdminMenu();
    </script>
</body>
</html>