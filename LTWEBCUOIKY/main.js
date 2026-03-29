// File: main.js

document.addEventListener('DOMContentLoaded', function() {
    updateHeaderUser();
    updateCartCount();
});

// 1. HEADER LOGIC
function updateHeaderUser() {
    const userLinksDiv = document.getElementById('user-links');
    // Kiểm tra xem phần tử có tồn tại không trước khi gán
    if (userLinksDiv) {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser) {
            userLinksDiv.innerHTML = `
                <span>Chào, <b>${currentUser.username}</b></span> | 
                <a href="#" onclick="logout()" style="color: red; text-decoration: none;">Thoát</a>
            `;
        } else {
            userLinksDiv.innerHTML = `
                <a href="dangnhap.html" style="color: #333; text-decoration: none;">Đăng nhập</a> | 
                <a href="dangky.html" style="color: #333; text-decoration: none;">Đăng ký</a>
            `;
        }
    }
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const countElement = document.getElementById('cart-count');
    if (countElement) {
        countElement.innerText = cart.reduce((acc, item) => acc + item.quantity, 0);
    }
}

function logout() {
    localStorage.removeItem('currentUser');
    alert('Đã đăng xuất!');
    window.location.reload();
}

// 2. LOGIC THÊM VÀO GIỎ
function addToCart(id, name, price, image) {
    // Chuyển đổi giá thành số (đề phòng trường hợp truyền vào chuỗi)
    let finalPrice = Number(price);
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id: id, name: name, price: finalPrice, image: image, quantity: 1 });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert(`Đã thêm "${name}" vào giỏ!`);
}