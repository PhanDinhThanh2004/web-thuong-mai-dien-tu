document.addEventListener('DOMContentLoaded', function() {
    updateHeaderUser();
    updateCartCount();

    const searchInput = document.getElementById('search-input');

    // 1. Bắt sự kiện nhấn Enter
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch();
            }
        });
    }

    // 2. TỰ ĐỘNG LỌC KHI TRANG VỪA TẢI XONG
    // Kiểm tra xem trên đường dẫn có chữ ?tim=... không
    const urlParams = new URLSearchParams(window.location.search);
    const keyword = urlParams.get('tim'); 

    if (keyword && searchInput) {
        // Điền từ khóa vào ô tìm kiếm
        searchInput.value = keyword;
        
        // Gọi hàm lọc ngay lập tức
        filterProductsLocal(keyword);
    }
});

// --- HÀM LỌC SẢN PHẨM (Chạy ngay tại trang, không tải lại) ---
function filterProductsLocal(keyword) {
    const term = keyword.toLowerCase().trim();
    const products = document.querySelectorAll('.product');
    
    if (products.length === 0) return;

    products.forEach(product => {
        // Lấy dữ liệu ẩn trong data-name
        const info = product.getAttribute('data-name');
        
        if (info) {
            // So sánh (chuyển hết về chữ thường)
            if (info.toLowerCase().includes(term)) {
                product.style.display = 'flex'; // Tìm thấy -> Hiện
            } else {
                product.style.display = 'none'; // Không giống -> Ẩn
            }
        }
    });
}

// --- HÀM XỬ LÝ KHI BẤM NÚT TÌM ---
function handleSearch() {
    const input = document.getElementById('search-input');
    if (!input) return;
    
    const term = input.value.trim();

    // Nếu đang ở trang sản phẩm -> Lọc ngay tại chỗ
    if (window.location.pathname.includes('sanpham.php')) {
        filterProductsLocal(term);
    } else {
        // Nếu đang ở trang khác -> Chuyển sang trang sản phẩm
        if(term) {
            window.location.href = 'sanpham.php?tim=' + encodeURIComponent(term);
        }
    }
}

// --- CÁC HÀM CŨ (Giữ nguyên) ---
function updateHeaderUser() {
    const userLinksDiv = document.getElementById('user-links');
    if (userLinksDiv) {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser) {
            let adminBtn = currentUser.role == 1 ? `<a href="admin/index.php" style="color:#ff6600;margin-right:10px;text-decoration:none;font-weight:bold">⚙️ Quản Trị</a> | ` : '';
            userLinksDiv.innerHTML = `${adminBtn} <span style="margin:0 10px">Chào, <b>${currentUser.username}</b></span> <a href="#" onclick="logout()" style="color:red;text-decoration:none">Đăng xuất</a>`;
        } else {
            userLinksDiv.innerHTML = `<a href="dangnhap.php">Đăng nhập</a> | <a href="dangky.php">Đăng ký</a>`;
        }
    }
}

function logout() {
    if(confirm('Đăng xuất?')) {
        localStorage.removeItem('currentUser');
        // fetch('logout.php'); // Nếu có file logout
        window.location.href = 'index.html';
    }
}

function updateCartCount() {
    const countElement = document.getElementById('cart-count');
    if (!countElement) return;
    const currentUser = localStorage.getItem('currentUser');
    if (!currentUser) { countElement.innerText = 0; return; }

    let formData = new FormData(); formData.append('action', 'list');
    fetch('api_cart.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(data => {
        let total = Array.isArray(data) ? data.reduce((acc, item) => acc + parseInt(item.quantity), 0) : 0;
        countElement.innerText = total;
    }).catch(() => countElement.innerText = 0);
}

function addToCart(id, name, price, image) {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (!currentUser) {
        alert("Bạn phải ĐĂNG NHẬP để mua hàng!");
        window.location.href = 'dangnhap.php';
        return;
    }
    let formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', id);

    fetch('api_cart.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(data => {
        alert(data.message);
        updateCartCount();
    });
}