<?php
session_start();
include '../db.php';

// Kiểm tra Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: ../dangnhap.php");
    exit();
}

// Xử lý Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $old_price = $_POST['old_price'];
    $desc = $_POST['description'];

    $target_dir = "../img/"; 
    $image1 = "img/" . basename($_FILES["image1"]["name"]);
    move_uploaded_file($_FILES["image1"]["tmp_name"], "../" . $image1);

    $image2 = "img/" . basename($_FILES["image2"]["name"]);
    move_uploaded_file($_FILES["image2"]["tmp_name"], "../" . $image2);

    $sql = "INSERT INTO products (brand, name, description, price, old_price, image, image2) 
            VALUES ('$brand', '$name', '$desc', '$price', '$old_price', '$image1', '$image2')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Thêm thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm | Admin</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2 style="padding-bottom: 0; border-bottom: none;">
            <a href="../index.html" style="text-decoration: none; color: #ff6600; display: block; padding-bottom: 15px; border-bottom: 1px solid #465b6e;">
                TIMEHOUSE
            </a>
        </h2>
        <a href="index.php" class="active"><i class="fas fa-box"></i> Quản lý Sản Phẩm</a>
        <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a>
        <a href="../sanpham.php" target="_blank"><i class="fas fa-globe"></i> Xem Trang Web</a>
    </div>

    <div class="main-content">
        <div class="header-panel">
            <h1>Thêm Sản Phẩm Mới</h1>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-container">
                
                <div class="form-row">
                    <div class="form-label">Tên sản phẩm <span style="color:red">*</span></div>
                    <div class="form-field">
                        <input type="text" name="name" required placeholder="Ví dụ: Rolex Submariner...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Thương hiệu</div>
                    <div class="form-field">
                        <select name="brand">
                            <option value="Rolex">Rolex</option>
                            <option value="Casio">Casio</option>
                            <option value="Omega">Omega</option>
                            <option value="Seiko">Seiko</option>
                            <option value="Citizen">Citizen</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Giá bán (VNĐ) <span style="color:red">*</span></div>
                    <div class="form-field">
                        <input type="number" name="price" required placeholder="Nhập giá chính thức...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Giá cũ (Nếu có)</div>
                    <div class="form-field">
                        <input type="number" name="old_price" value="0" placeholder="Giá trước giảm...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Mô tả sản phẩm</div>
                    <div class="form-field">
                        <textarea name="description" rows="4" placeholder="Thông tin chi tiết..."></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Ảnh đại diện <span style="color:red">*</span></div>
                    <div class="form-field">
                        <input type="file" name="image1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-label">Ảnh phụ (Hover)</div>
                    <div class="form-field">
                        <input type="file" name="image2" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-cancel">Hủy bỏ</a>
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Lưu lại</button>
                </div>

            </div>
        </form>
    </div>

</body>
</html>