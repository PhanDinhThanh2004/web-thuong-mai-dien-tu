<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thương Hiệu | TimeHouse</title>
</head>
<link rel="stylesheet" href="style.css" type ="text/css">
<body>

    <header>
        <div class="top-header">
            <a href="index.html" class="logo">TIMEHOUSE</a>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Tìm kiếm...">
                <button onclick="window.location.href='sanpham.php'" style="border:none; background:none; cursor:pointer">🔍</button>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <a href="giohang.php" style="text-decoration: none; color: #333;">Giỏ hàng (<b id="cart-count" style="color: #ff6600;">0</b>)</a>
                <div id="user-links"></div>
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

    <section class="brands-section">
        <h2 class="section-title">ĐỐI TÁC CHIẾN LƯỢC</h2>
        
        <div class="brands-list">
            <div class="brand active" onclick="showBrand('rolex', this)">
                <img src="img/rolex1.jpg" onerror="this.src='https://via.placeholder.com/150?text=ROLEX'">
                <div class="brand-name">ROLEX</div>
            </div>
            <div class="brand" onclick="showBrand('casio', this)">
                <img src="img/casio1.jpg" onerror="this.src='https://via.placeholder.com/150?text=CASIO'">
                <div class="brand-name">CASIO</div>
            </div>
            <div class="brand" onclick="showBrand('omega', this)">
                <img src="img/omega1.jpg" onerror="this.src='https://via.placeholder.com/150?text=OMEGA'">
                <div class="brand-name">OMEGA</div>
            </div>
            <div class="brand" onclick="showBrand('seiko', this)">
                <img src="img/seiko1.jpg" onerror="this.src='https://via.placeholder.com/150?text=SEIKO'">
                <div class="brand-name">SEIKO</div>
            </div>
            <div class="brand" onclick="showBrand('citizen', this)">
                <img src="img/citizen1.jpg" onerror="this.src='https://via.placeholder.com/150?text=CITIZEN'">
                <div class="brand-name">CITIZEN</div>
            </div>
        </div>
        
        <div class="brand-detail" id="detail-box">
            <div class="brand-logo-large">
                <img id="d-img" src="img/rolex1.jpg" onerror="this.src='https://via.placeholder.com/300?text=Logo'">
            </div>
            <div class="brand-description">
                <h3 id="d-name">ROLEX - ĐẲNG CẤP THƯỢNG LƯU</h3>
                <p id="d-desc">
                    Rolex là thương hiệu đồng hồ Thụy Sĩ nổi tiếng nhất thế giới, biểu tượng của địa vị, sự sang trọng và độ chính xác tuyệt đối. 
                    Được thành lập từ năm 1905, Rolex tiên phong trong việc phát minh đồng hồ đeo tay chống nước đầu tiên (Oyster) và cơ chế tự lên dây cót (Perpetual).
                </p>
                <a href="sanpham.php?tim=rolex" id="d-link" class="btn-view-all">Xem các mẫu ROLEX</a>
            </div>
        </div>
    </section>

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

    <script src="main.js?v=<?php echo time(); ?>"></script>
    
    <script>
        // Dữ liệu nội dung
        const brandData = {
            'rolex': {
                name: 'ROLEX - ĐẲNG CẤP THƯỢNG LƯU',
                img: 'img/rolex1.jpg',
                link: 'sanpham.php?tim=rolex',
                desc: 'Rolex là thương hiệu đồng hồ Thụy Sĩ nổi tiếng nhất thế giới, biểu tượng của địa vị và sự sang trọng. Các dòng nổi bật: Submariner, Datejust, Daytona.'
            },
            'casio': {
                name: 'CASIO - BỀN BỈ VÀ ĐA NĂNG',
                img: 'img/casio1.jpg',
                link: 'sanpham.php?tim=casio',
                desc: 'Thương hiệu Nhật Bản quốc dân, nổi tiếng với độ bền "nồi đồng cối đá". Dòng G-Shock huyền thoại chống va đập, chống nước cực tốt, phù hợp với giới trẻ năng động.'
            },
            'omega': {
                name: 'OMEGA - CHINH PHỤC KHÔNG GIAN',
                img: 'img/omega1.jpg',
                link: 'sanpham.php?tim=omega',
                desc: 'Thương hiệu Thụy Sĩ gắn liền với lịch sử: Đồng hồ đầu tiên lên mặt trăng, đối tác của Olympic và James Bond. Nổi tiếng với bộ máy Co-Axial chính xác.'
            },
            'seiko': {
                name: 'SEIKO - TINH HOA NHẬT BẢN',
                img: 'img/seiko1.jpg',
                link: 'sanpham.php?tim=seiko',
                desc: 'Seiko là "ông tổ" của đồng hồ thạch anh (Quartz). Từ dòng Seiko 5 bền bỉ đến Grand Seiko xa xỉ, hãng luôn mang đến sự hoàn thiện tỉ mỉ.'
            },
            'citizen': {
                name: 'CITIZEN - CÔNG NGHỆ ECO-DRIVE',
                img: 'img/citizen1.jpg',
                link: 'sanpham.php?tim=citizen',
                desc: 'Tiên phong với công nghệ Eco-Drive (năng lượng ánh sáng), giúp người dùng không bao giờ phải thay pin. Thiết kế thanh lịch và thân thiện môi trường.'
            }
        };

        // Hàm xử lý Click (Đã thêm kiểm tra an toàn)
        function showBrand(key, el) {
            const data = brandData[key];
            
            // 1. Lấy các phần tử HTML
            const box = document.getElementById('detail-box');
            const img = document.getElementById('d-img');
            const name = document.getElementById('d-name');
            const desc = document.getElementById('d-desc');
            const link = document.getElementById('d-link');

            // 2. Kiểm tra an toàn: Nếu thiếu phần tử nào thì dừng lại, không báo lỗi
            if (!box || !img || !name || !desc || !link) {
                console.error("Lỗi: Không tìm thấy khung chi tiết trong HTML!");
                return;
            }
            
            // 3. Hiệu ứng mờ
            box.style.opacity = 0.5;
            
            setTimeout(() => {
                // 4. Cập nhật nội dung
                img.src = data.img;
                name.innerText = data.name;
                desc.innerText = data.desc;
                
                // Cập nhật nút xem sản phẩm
                link.href = data.link;
                link.innerText = "Xem các mẫu " + key.toUpperCase();

                // Hiện lại
                box.style.opacity = 1;
            }, 150);

            // 5. Đổi viền cam (class active)
            document.querySelectorAll('.brand').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>