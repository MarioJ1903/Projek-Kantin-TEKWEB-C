<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Admin Panel</a>
            <div class="ms-auto">
                <a href="../public/menu.php" class="btn btn-outline-light btn-sm me-2" target="_blank">Lihat Web</a>
                <a href="../api/auth_api.php?action=logout" class="btn btn-danger btn-sm">Logout <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary fw-bold">Kelola Menu</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Menu
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th> <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-menu-list">
                            </tbody>
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
                        <div class="mb-3">
                            <label>Nama Menu</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Stok Awal</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Foto Menu</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load Data
        async function loadAdminMenu() {
            try {
                const res = await fetch('../api/menu_api.php?action=read');
                const data = await res.json();
                let html = '';
                
                data.forEach(item => {
                    // Logic Gambar (Pakai default jika kosong)
                    let imgSrc = item.image && item.image !== 'default.jpg' 
                        ? `../assets/img/${item.image}` 
                        : `https://placehold.co/100x100?text=${item.name.substring(0,3)}`;

                    html += `
                    <tr>
                        <td>
                            <img src="${imgSrc}" class="rounded" width="50" height="50" style="object-fit:cover">
                        </td>
                        <td>${item.name}</td>
                        <td>Rp ${item.price}</td>
                        <td>${item.stock}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteMenu(${item.menu_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                document.getElementById('admin-menu-list').innerHTML = html;
            } catch (error) {
                console.error(error);
            }
        }

        // Add Data (Support Upload Gambar)
        document.getElementById('formAddMenu').addEventListener('submit', async function(e){
            e.preventDefault();
            
            // FormData otomatis menangkap file input juga
            const formData = new FormData(this);
            
            try {
                const res = await fetch('../api/menu_api.php?action=create', {
                    method: 'POST',
                    body: formData // Jangan set Content-Type header manual saat pakai FormData!
                });
                
                const result = await res.json();
                
                if(result.status === 'success') {
                    // Tutup modal & refresh
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
                    modal.hide();
                    this.reset();
                    loadAdminMenu();
                    alert("Menu berhasil ditambahkan!");
                } else {
                    alert("Gagal: " + result.message);
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan sistem.");
            }
        });

        // Delete Data
        async function deleteMenu(id) {
            if(!confirm('Hapus menu ini?')) return;
            
            const formData = new FormData();
            formData.append('id', id);

            await fetch('../api/menu_api.php?action=delete', {
                method: 'POST',
                body: formData
            });
            loadAdminMenu();
        }

        loadAdminMenu();
    </script>
</body>
</html>