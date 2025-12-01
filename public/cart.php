<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .table-cart th { background-color: #e9ecef; }
        .qris-container { text-align: center; padding: 20px; }
        .qris-img { max-width: 250px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">E-Kantin</a>
            <div class="ms-auto">
                <a href="menu.php" class="btn btn-outline-light btn-sm">Kembali ke Menu</a>
            </div>
        </div>
    </nav>

    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 800px;">
        <h3 class="mb-4"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h3>
        
        <div class="table-responsive">
            <table class="table table-cart align-middle">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="cart-list">
                    </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <h4 class="fw-bold" id="total-price">Total: Rp 0</h4>
            <button onclick="showPaymentModal()" class="btn btn-success btn-lg shadow">
                <i class="fas fa-money-bill-wave me-2"></i> Bayar Sekarang
            </button>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-qrcode me-2"></i>Scan QRIS Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="qris-container">
                        <p class="mb-2">Silakan scan QR Code di bawah ini:</p>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" alt="QRIS" class="qris-img mb-3">
                        <h4 class="text-primary fw-bold" id="modal-total-price">Rp 0</h4>
                        <p class="text-muted small">E-Kantin Merchant</p>
                        
                        <div class="alert alert-warning small">
                            <i class="fas fa-info-circle"></i> Ini adalah simulasi. Anda tidak perlu membayar sungguhan. Klik tombol di bawah untuk menyelesaikan pesanan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="processCheckout()" class="btn btn-success w-100">
                        <i class="fas fa-check-circle me-1"></i> Saya Sudah Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentTotal = 0;

        // 1. Load Data Cart
        async function loadCart() {
            try {
                const res = await fetch('../api/cart_api.php?action=read');
                const data = await res.json();
                
                let html = '';
                currentTotal = 0;
                
                if (data.length === 0) {
                    document.getElementById('cart-list').innerHTML = '<tr><td colspan="4" class="text-center py-4">Keranjang kosong. <a href="menu.php">Belanja yuk!</a></td></tr>';
                    document.getElementById('total-price').innerText = 'Total: Rp 0';
                    return;
                }

                data.forEach(item => {
                    let sub = item.price * item.quantity;
                    currentTotal += sub;
                    // Format Rupiah
                    let priceRp = new Intl.NumberFormat('id-ID').format(item.price);
                    let subRp = new Intl.NumberFormat('id-ID').format(sub);
                    
                    html += `
                    <tr>
                        <td>
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">Makanan</small>
                        </td>
                        <td>Rp ${priceRp}</td>
                        <td><span class="badge bg-secondary">${item.quantity}</span></td>
                        <td class="fw-bold text-success">Rp ${subRp}</td>
                    </tr>`;
                });

                document.getElementById('cart-list').innerHTML = html;
                document.getElementById('total-price').innerText = 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(currentTotal);
            } catch (error) {
                console.error("Gagal load cart", error);
            }
        }

        // 2. Tampilkan Modal QRIS
        function showPaymentModal() {
            if (currentTotal === 0) {
                alert("Keranjang Anda kosong!");
                return;
            }
            // Update harga di dalam modal
            document.getElementById('modal-total-price').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentTotal);
            
            // Tampilkan modal Bootstrap
            let myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            myModal.show();
        }

        // 3. Proses Checkout (Setelah klik "Sudah Bayar")
        async function processCheckout() {
            // Ubah tombol jadi loading agar tidak diklik 2x
            const btn = event.target;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
            btn.disabled = true;

            try {
                const res = await fetch('../api/order_api.php?action=checkout');
                const data = await res.json();

                if(data.status) {
                    // Tutup modal
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    
                    // Alert Sukses
                    alert('✅ Pembayaran Berhasil! Terima kasih.');
                    
                    // Redirect ke riwayat
                    window.location.href = 'orders.php';
                } else {
                    alert('Gagal Checkout: ' + data.message);
                    btn.innerHTML = 'Coba Lagi';
                    btn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan sistem.");
            }
        }

        // Load saat halaman dibuka
        loadCart();
    </script>
</body>
</html>