<?php
session_start();
include '../db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: ../dangnhap.php");
    exit();
}

// 1. DUYỆT ĐƠN
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE orders SET status = 'Đã thanh toán' WHERE id = $id");
    header("Location: admin_orders.php");
    exit;
}
// 2. VẬN CHUYỂN
if (isset($_GET['action']) && $_GET['action'] == 'ship' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE orders SET status = 'Đã vận chuyển' WHERE id = $id");
    header("Location: admin_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn Hàng | Admin</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; color: white; display: inline-block;}
        .bg-pending { background-color: #f39c12; } 
        .bg-paid { background-color: #27ae60; }    
        .bg-shipped { background-color: #2980b9; } 
        .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; color: white; transition: 0.3s; }
        .btn-approve { background-color: #27ae60; } 
        .btn-ship { background-color: #e67e22; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="padding-bottom: 0; border-bottom: none;">
            <a href="../index.html" style="text-decoration: none; color: #ff6600; display: block; padding-bottom: 15px; border-bottom: 1px solid #465b6e;">TIMEHOUSE</a>
        </h2>
        <a href="index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a>
        <a href="admin_orders.php" style="background-color: #ff6600; color: white;"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a>
        <a href="../sanpham.php" target="_blank"><i class="fas fa-globe"></i> Xem Trang Web</a>
        <a href="#" onclick="if(confirm('Đăng xuất?')) location.href='../dangnhap.php'" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
    </div>

    <div class="main-content">
        <div class="header-panel">
            <h1>Quản Lý Đơn Hàng</h1>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Tổng Tiền</th> <th>Trạng Thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $oid = $row['id'];
                            
                            // LẤY CHI TIẾT SẢN PHẨM TRONG ĐƠN HÀNG NÀY
                            $d_res = mysqli_query($conn, "SELECT * FROM order_details WHERE order_id = $oid");
                            
                            // --- BẮT ĐẦU TÍNH TOÁN LẠI TỪ ĐẦU ---
                            $TONG_TIEN_THAT_SU = 0; // Biến này để chứa tổng tiền cộng dồn
                            $danh_sach_sp = [];     // Biến này để lưu tên sản phẩm hiển thị
                            
                            while($item = mysqli_fetch_assoc($d_res)) {
                                // 1. Làm sạch giá (Xóa dấu chấm: "5.000.000" -> 5000000)
                                $gia_sach = preg_replace('/[^0-9]/', '', $item['price']);
                                
                                // 2. Lấy số lượng
                                $so_luong = (int)$item['quantity'];
                                
                                // 3. CỘNG DỒN: Tổng += (Giá x Số lượng)
                                $TONG_TIEN_THAT_SU += ((int)$gia_sach * $so_luong);
                                
                                // Lưu lại để hiển thị ra list
                                $danh_sach_sp[] = $item;
                            }
                            // --- KẾT THÚC TÍNH TOÁN ---
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['fullname']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['phone']); ?></small><br>
                            <small style="color:#777"><?php echo date("d/m/Y H:i", strtotime($row['created_at'])); ?></small>
                        </td>
                        <td>
                            <ul style="margin:0;padding-left:15px;font-size:13px;color:#444">
                                <?php foreach($danh_sach_sp as $sp): ?>
                                    <li><?php echo $sp['product_name'] . " <b>x" . $sp['quantity'] . "</b>"; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        
                        <td style="color:#d32f2f; font-weight:bold; font-size: 15px;">
                            <?php echo number_format($TONG_TIEN_THAT_SU, 0, ',', '.'); ?>đ
                        </td>
                        
                        <td>
                            <?php if($row['status'] == 'Chờ duyệt'): ?>
                                <span class="badge bg-pending">Chờ duyệt</span>
                            <?php elseif($row['status'] == 'Đã thanh toán'): ?>
                                <span class="badge bg-paid">Đã thanh toán</span>
                            <?php elseif($row['status'] == 'Đã vận chuyển'): ?>
                                <span class="badge bg-shipped">Đã vận chuyển</span>
                            <?php else: ?>
                                <span class="badge" style="background:#999"><?php echo $row['status']; ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($row['status'] == 'Chờ duyệt'): ?>
                                <a href="admin_orders.php?action=approve&id=<?php echo $row['id']; ?>" 
                                   class="btn-action btn-approve"
                                   onclick="return confirm('Duyệt đơn này?')">
                                   <i class="fas fa-check"></i> Duyệt
                                </a>
                            <?php elseif($row['status'] == 'Đã thanh toán'): ?>
                                <a href="admin_orders.php?action=ship&id=<?php echo $row['id']; ?>" 
                                   class="btn-action btn-ship"
                                   onclick="return confirm('Giao hàng?')">
                                   <i class="fas fa-truck"></i> Vận chuyển
                                </a>
                            <?php elseif($row['status'] == 'Đã vận chuyển'): ?>
                                <span style="color:#2980b9; font-weight:bold;">
                                    <i class="fas fa-check-circle"></i> Đã giao đi
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Chưa có đơn hàng nào!</td></tr>";
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>