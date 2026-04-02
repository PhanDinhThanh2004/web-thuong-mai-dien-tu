document.addEventListener('DOMContentLoaded', () => {
    // Gọi hàm hiển thị tóm tắt đơn hàng khi vừa vào trang
    renderSummary();

    // Bắt sự kiện khi người dùng bấm nút Đặt Hàng (Submit form)
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn chặn trang bị reload lại
            placeOrder();
        });
    }
});

// Hàm lấy dữ liệu giỏ hàng và hiển thị ra bảng tóm tắt
function renderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const tbody = document.getElementById('summary-body');
    let total = 0;

    // Nếu giỏ hàng trống mà cố tình vào trang này thì đá về trang chủ
    if (cart.length === 0) {
        alert("Giỏ hàng trống, đang quay lại trang chủ...");
        window.location.href = 'index.html';
        return;
    }

    tbody.innerHTML = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        tbody.innerHTML += `
            <tr>
                <td style="font-size: 14px; font-weight: bold;">${item.name}</td>
                <td style="text-align: center; color: #555;">x${item.quantity}</td>
                <td style="text-align: right; color: #d32f2f; font-weight: bold;">${itemTotal.toLocaleString('vi-VN')}đ</td>
            </tr>
        `;
    });

    const totalEl = document.getElementById('summary-total');
    totalEl.innerText = `${total.toLocaleString('vi-VN')}đ`;
    totalEl.dataset.total = total; // Lưu lại dạng số vào thuộc tính data để dễ lấy khi đặt hàng
}

// Hàm xử lý việc đặt hàng và lưu vào LocalStorage
function placeOrder() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalEl = document.getElementById('summary-total');
    const total = parseInt(totalEl.dataset.total);

    // 1. Lấy thông tin khách hàng nhập từ form
    const customerName = document.getElementById('cust-name').value;
    const phone = document.getElementById('cust-phone').value;
    const email = document.getElementById('cust-email').value;
    const address = document.getElementById('cust-address').value;
    const paymentMethod = document.getElementById('payment-method').value;

    // 2. Tạo đối tượng đơn hàng theo đúng format JSON trưởng nhóm yêu cầu
    const newOrder = {
        customer: customerName,
        phone: phone,
        address: address,
        email: email,
        paymentMethod: paymentMethod,
        cart: cart,
        total: total,
        date: new Date().toISOString().split('T')[0] // Trả về dạng YYYY-MM-DD
    };

    // 3. Lấy mảng orders cũ ra (nếu có), thêm đơn mới vào, rồi lưu lại
    let orders = JSON.parse(localStorage.getItem('orders')) || [];
    orders.push(newOrder);
    localStorage.setItem('orders', JSON.stringify(orders));

    // 4. Xóa giỏ hàng sau khi đặt thành công
    localStorage.removeItem('cart');

    // 5. Hiển thị thông báo và chuyển hướng về trang chủ
    alert('Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại TimeHouse.');
    window.location.href = 'index.html';
}