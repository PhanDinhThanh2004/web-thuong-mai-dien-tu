<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm | TimeHouse</title>
</head>
<link rel="stylesheet" href="style.css" type ="text/css">
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

    <div class="container">
        <h2 style="text-align: center; margin-bottom: 30px;">TẤT CẢ SẢN PHẨM</h2>
        
        <div class="product-list">
            <?php
            $sql = "SELECT * FROM products";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // --- BÍ KÍP Ở ĐÂY: Nối cả Brand và Name vào biến tìm kiếm ---
                    // Ví dụ: "Rolex Submariner"
                    $searchKey = $row['brand'] . " " . $row['name'];
            ?>
                <div class="product" data-name="<?php echo $searchKey; ?>">
                    
                    <div class="product-img-box">
                        <img src="<?php echo $row['image']; ?>" class="img-main">
                        <img src="<?php echo !empty($row['image2']) ? $row['image2'] : $row['image']; ?>" class="img-hover">
                    </div>
                    <div class="info">
                        <div class="brand"><?php echo $row['brand']; ?></div>
                        <div class="name"><?php echo $row['name']; ?></div>
                        <div class="price">
                            <?php if($row['old_price'] > 0): ?>
                                <span class="old-price"><?php echo number_format($row['old_price'], 0, ',', '.'); ?>đ</span>
                            <?php endif; ?>
                            <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                        </div>
                        
                        <button class="order-btn" onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $row['price']; ?>, '<?php echo $row['image']; ?>')">THÊM VÀO GIỎ</button>
                    </div>
                </div>
            <?php
                }
            } else { echo "<p style='text-align:center; width:100%'>Chưa có dữ liệu.</p>"; }
            ?>
        </div>
    </div>

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
                    <li>Địa chỉ: nhóm 10 lập trình web trường uth </li>
                    <li>Hotline: 1900 000 </li>
                    <li>Email: infor@timehouse.vn</li>
                    <li>Giờ làm việc: 8:00 - 22:00</li>
                </ul>
            </div>
        </div>
    </footer>
    <script>
        function addToCart(productId) {
            let formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);

            fetch('api_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    alert(data.message); // Báo lỗi nếu chưa đăng nhập
                    window.location.href = 'dangnhap.php'; // Chuyển trang đăng nhập
                } else {
                alert(data.message); // Báo thành công
            // Nếu muốn cập nhật số lượng trên icon giỏ hàng ngay lập tức thì gọi hàm loadCartCount() ở đây
                }
            })
            .catch(error => console.error('Error:', error));
        }       
    </script>
    
    <script src="main.js?v=<?php echo time(); ?>"></script>
</body>
</html>