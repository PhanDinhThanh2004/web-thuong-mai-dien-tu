<?php
include 'db.php';

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    header('Location: sanpham.php');
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    'SELECT id, brand, name, description, price, old_price, image, image2 FROM products WHERE id = ?'
);

mysqli_stmt_bind_param($stmt, 'i', $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='sanpham.php';</script>";
    exit;
}

$description = trim((string) $product['description']);
$descriptionParts = explode('---', $description, 2);
$description = trim(count($descriptionParts) === 2 ? $descriptionParts[1] : $description);

if ($description === '') {
    $description = 'Thông tin sản phẩm đang được cập nhật.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | TimeHouse</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <header>
        <div class="top-header">
            <a href="index.html" class="logo">TIMEHOUSE</a>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Tìm kiếm đồng hồ...">
                <button onclick="handleSearch()" style="border:none; background:none; cursor:pointer;">🔍</button>
            </div>
            <div class="header-actions" style="display: flex; align-items: center; gap: 15px;">
                <a href="giohang.php" style="text-decoration: none; color: #333;">Giỏ hàng (<b id="cart-count" style="color: #ff6600;">0</b>)</a>
                <div id="user-links">
                    <a href="dangnhap.php">Đăng nhập</a> | <a href="dangky.php">Đăng ký</a>
                </div>
            </div>
        </div>
        <nav>
            <a href="index.html">TRANG CHỦ</a>
            <a href="sanpham.php">SẢN PHẨM</a>
            <a href="thuonghieu.php">THƯƠNG HIỆU</a>
            <a href="gioithieu.html">GIỚI THIỆU</a>
            <a href="lienhe.html">LIÊN HỆ</a>
        </nav>
    </header>

    <main class="detail-page">
        <div class="detail-shell">
            <div class="detail-gallery">
                <img
                    src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="detail-main-image"
                >
                <?php if (!empty($product['image2'])): ?>
                    <div class="detail-side-images">
                        <img
                            src="<?php echo htmlspecialchars($product['image2']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?> - ảnh phụ"
                        >
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-panel">
                <a href="sanpham.php" class="detail-back-link">← Quay lại trang sản phẩm</a>
                <div class="detail-brand"><?php echo htmlspecialchars((string) $product['brand']); ?></div>
                <h1 class="detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="detail-price">
                    <?php if ((int) $product['old_price'] > 0): ?>
                        <span class="detail-old-price"><?php echo number_format((int) $product['old_price'], 0, ',', '.'); ?>đ</span>
                    <?php endif; ?>
                    <span><?php echo number_format((int) $product['price'], 0, ',', '.'); ?>đ</span>
                </div>

                <div class="detail-meta">
                    <span>Ảnh sản phẩm thực tế</span>
                    <span>Hỗ trợ bảo hành chính hãng</span>
                </div>

                <section class="detail-description">
                    <h2>Mô tả sản phẩm</h2>
                    <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
                </section>

                <div class="detail-actions">
                    <button class="order-btn" onclick="addToCart(<?php echo (int) $product['id']; ?>)">THÊM VÀO GIỎ</button>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>VỀ CHÚNG TÔI</h3>
                <ul>
                    <li><a href="gioithieu.html">Giới thiệu</a></li>
                    <li><a href="lienhe.html">Hệ thống cửa hàng</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>CHÍNH SÁCH</h3>
                <ul>
                    <li><a href="lienhe.html">Chính sách bảo hành</a></li>
                    <li><a href="lienhe.html">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                <ul>
                    <li><a href="lienhe.html">Liên hệ</a></li>
                    <li><a href="giohang.php">Phương thức thanh toán</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>THÔNG TIN LIÊN HỆ</h3>
                <ul>
                    <li>Địa chỉ: nhóm 10 lập trình web trường uth</li>
                    <li>Hotline: 1900 000</li>
                    <li>Email: infor@timehouse.vn</li>
                    <li>Giờ làm việc: 8:00 - 22:00</li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="main.js?v=<?php echo time(); ?>"></script>
    <script>
        function addToCart(productId) {
            let formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);

            fetch('api_cart.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert(data.message);
                        window.location.href = 'dangnhap.php';
                        return;
                    }

                    alert(data.message);
                    updateCartCount();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
