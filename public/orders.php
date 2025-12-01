<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Riwayat Pesanan</h2>
        <a href="menu.php" class="btn btn-secondary mb-3">Kembali</a>
        <div class="list-group" id="order-list"></div>
    </div>
    <script>
        async function loadOrders() {
            const res = await fetch('../api/order_api.php?action=history');
            const data = await res.json();
            let html = '';
            data.forEach(o => {
                html += `
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Order #${o.order_id}</h5>
                        <small>${o.created_at}</small>
                    </div>
                    <p class="mb-1">Total: Rp ${o.total_price}</p>
                    <span class="badge bg-success">${o.status}</span>
                </div>`;
            });
            document.getElementById('order-list').innerHTML = html;
        }
        loadOrders();
    </script>
</body>
</html>