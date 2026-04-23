<?php
session_start();
include '../db.php';

// Kiểm tra Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: ../dangnhap.php");
    exit();
}

// Xử lý Xóa sản phẩm
if (isset($_GET['xoa'])) {
    $id = $_GET['xoa'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Admin</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2 style="padding-bottom: 0; border-bottom: none;">
            <a href="../index.html" style="text-decoration: none; color: #ff6600; display: block; padding-bottom: 15px; border-bottom: 1px solid #465b6e;">
                TIMEHOUSE
            </a>
        </h2>
        
        <a href="index.php" style="background-color: #ff6600; color: white;">
            <i class="fas fa-box"></i> Quản lý Sản phẩm
        </a>

        <a href="admin_orders.php">
            <i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng
        </a>

        <a href="../sanpham.php" target="_blank">
            <i class="fas fa-globe"></i> Xem Trang Web
        </a>
        <a href="#" onclick="dangXuat()" class="logout">
            <i class="fas fa-sign-out-alt"></i> Đăng Xuất
        </a>
    </div>

    <div class="main-content">
        <div class="header-panel">
            <h1>Danh Sách Sản Phẩm</h1>
            <a href="them.php" class="btn btn-add">
                <i class="fas fa-plus"></i> Thêm Mới
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">Ảnh</th>
                        <th width="35%">Tên sản phẩm</th>
                        <th width="15%">Giá tiền</th>
                        <th width="15%">Thương hiệu</th>
                        <th width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM products ORDER BY id ASC";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>#" . $row['id'] . "</td>";
                            echo "<td><img src='../" . $row['image'] . "' onerror=\"this.src='https://via.placeholder.com/50'\"></td>";
                            echo "<td><strong>" . $row['name'] . "</strong></td>";
                            echo "<td style='color:#d32f2f; font-weight:bold;'>" . number_format($row['price']) . " đ</td>";
                            echo "<td>" . $row['brand'] . "</td>";
                            echo "<td>
                                <a href='index.php?xoa=" . $row['id'] . "' class='btn btn-del' onclick='return confirm(\"Bạn có chắc chắn muốn xóa?\")'>
                                    <i class='fas fa-trash'></i> Xóa
                                </a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Chưa có sản phẩm nào!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function dangXuat() {
            if(confirm('Bạn muốn đăng xuất khỏi Admin?')) {
                localStorage.removeItem('currentUser');
                window.location.href = '../dangnhap.php';
            }
        }
    </script>
</body>
</html>
