<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng | TimeHouse</title>
</head>
<link rel="stylesheet" href="style.css" type ="text/css">
<body>
<header>
    <div class="top-header">
        <a href="index.html" class="logo">TIMEHOUSE</a>
        <a href="giohang.php" style="text-decoration:none;color:#333;font-weight:bold;">
            Giỏ hàng (<span id="cart-count" style="color:#f60">0</span>)
        </a>
    </div>
</header>

<div class="cart-container">
    <h2>Giỏ Hàng Của Bạn</h2>
    <table id="cart-table">
        <thead><tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th><th>Xóa</th></tr></thead>
        <tbody id="cart-body"></tbody>
    </table>
    <div class="total-price" id="cart-total" data-value="0">Tổng cộng: 0đ</div>
    
    <div class="action-buttons">
        <a href="sanpham.php" class="btn-back">← Tiếp tục mua sắm</a>
        <button onclick="checkout()" class="btn-checkout">Thanh toán</button>
    </div>

    <div id="qr-box">
        <h3>Quét mã QR để thanh toán</h3>
        <img id="qr-img" src="" alt="QR Code">
        <p><b>Số tài khoản:</b> 123456789<br><b>Ngân hàng:</b> VCB<br><b>Chủ TK:</b> TimeHouse</p>
        <button onclick="confirmPayment()" class="btn-checkout">Tôi đã chuyển khoản</button>
    </div>
</div>

<script>
// Hàm định dạng số thành tiền (Ví dụ: 5000000 -> 5.000.000đ)
function formatMoney(n) {
    return parseInt(n).toLocaleString('vi-VN') + 'đ';
}

function loadCart() {
    let formData = new FormData();
    formData.append('action','list');
    
    fetch('api_cart.php', {method:'POST', body:formData})
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('cart-body');
        tbody.innerHTML = '';
        
        // Bắt lỗi từ Server
        if (data.status === 'error') {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:30px;color:red">${data.message}</td></tr>`;
            return;
        }

        let total = 0;
        let count = 0;

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px">Giỏ hàng trống!</td></tr>';
        } else {
            data.forEach(item => {
                // SERVER ĐÃ GỬI SỐ SẠCH, CHỈ CẦN DÙNG
                let price = parseFloat(item.price); 
                let quantity = parseInt(item.quantity);
                let itemTotal = price * quantity;
                
                total += itemTotal;
                count += quantity;
                
                tbody.innerHTML += `
                <tr>
                    <td><img src="${item.image}" class="cart-img" onerror="this.src='https://via.placeholder.com/60'"> <b>${item.name}</b></td>
                    <td>${formatMoney(price)}</td>
                    <td>
                        <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                        <span style="margin:0 10px">${quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                    </td>
                    <td style="color:red;font-weight:bold">${formatMoney(itemTotal)}</td>
                    <td><button class="btn-remove" onclick="removeItem(${item.id})">✕</button></td>
                </tr>`;
            });
        }
        document.getElementById('cart-total').innerText = `Tổng cộng: ${formatMoney(total)}`;
        document.getElementById('cart-total').dataset.value = total; 
        document.getElementById('cart-count').innerText = count;
    })
    .catch(err => console.error("Lỗi:", err));
}

function updateQty(id, change){
    let form = new FormData();
    form.append('action', 'update');
    form.append('cart_id', id);
    form.append('change', change);
    fetch('api_cart.php', {method:'POST', body:form}).then(r => r.json()).then(() => loadCart());
}

function removeItem(id){
    if(confirm('Xóa?')){
        let form = new FormData();
        form.append('action', 'remove');
        form.append('cart_id', id);
        fetch('api_cart.php', {method:'POST', body:form}).then(r => r.json()).then(() => loadCart());
    }
}

function checkout(){
    let total = document.getElementById('cart-total').dataset.value;
    if (total == 0) return alert('Giỏ hàng trống!');
    
    // Link VietQR
    let account = "123456789"; 
    let bank = "VCB"; 
    let content = "THANHTOAN";
    let qrUrl = `https://img.vietqr.io/image/${bank}-${account}-compact.jpg?amount=${total}&addInfo=${content}`;
    
    document.getElementById('qr-img').src = qrUrl;
    document.getElementById('qr-box').style.display = "block";
    document.getElementById('qr-box').scrollIntoView({behavior: "smooth"});
}

function confirmPayment(){
    if(!confirm('Xác nhận bạn đã chuyển khoản?')) return;

    let form = new FormData();
    form.append('action', 'checkout');
    fetch('api_cart.php', {method:'POST', body:form})
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success'){
            alert(data.message);
            document.getElementById('qr-box').style.display = "none";
            loadCart();
        } else {
            alert(data.message);
        }
    });
}

// Chạy lần đầu
loadCart();
</script>
</body>
</html>