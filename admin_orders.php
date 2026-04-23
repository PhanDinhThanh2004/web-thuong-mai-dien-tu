<?php
session_start();
include '../db.php';

// Thiết lập font tiếng Việt để tránh lỗi hiển thị
mysqli_set_charset($conn, 'utf8mb4');

// Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: ../dangnhap.php");
    exit();
}

// 1. XỬ LÝ DUYỆT ĐƠN
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE orders SET status = 'Đã thanh toán' WHERE id = $id");
    header("Location: admin_orders.php");
    exit;
}

// 2. XỬ LÝ VẬN CHUYỂN
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
        .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; color: white; transition: 0.3s; border: none; cursor: pointer; }
        .btn-approve { background-color: #27ae60; } 
        .btn-ship { background-color: #e67e22; }
        .table-container table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        .table-container th, .table-container td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .table-container th { background-color: #34495e; color: white; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="padding: 20px 0; text-align: center;">
            <a href="../index.php" style="text-decoration: none; color: #ff6600; font-size: 24px; font-weight: bold;">TIMEHOUSE</a>
        </h2>
        <a href="index.php"><i class="fas fa-box"></i> Quản lý Sản phẩm</a>
        <a href="admin_orders.php" style="background-color: #ff6600; color: white;"><i class="fas fa-shopping-cart"></i> Quản lý Đơn hàng</a>
        <a href="../index.php" target="_blank"><i class="fas fa-globe"></i> Xem Trang Web</a>
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
                        <th>Sản Phẩm (Tên x SL)</th>
                        <th>Tổng Tiền</th> 
                        <th>Trạng Thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sắp xếp đơn mới nhất lên đầu
                    $sql = "SELECT * FROM orders ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $oid = $row['id'];
                            
                            // TRUY VẤN CHI TIẾT SẢN PHẨM
                            $d_res = mysqli_query($conn, "SELECT * FROM order_details WHERE order_id = $oid");
                            
                            $tong_bill = 0;
                            $has_items = false;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $oid; ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['fullname']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></small><br>
                            <small style="color:#777"><?php echo date("d/m/Y H:i", strtotime($row['created_at'])); ?></small>
                        </td>
                        <td>
                            <ul style="margin:0;padding-left:15px;font-size:13px;color:#444">
                                <?php 
                                while($item = mysqli_fetch_assoc($d_res)): 
                                    $has_items = true;
                                    // Làm sạch giá để tính toán (đề phòng giá lưu có dấu chấm)
                                    $p_price = (int)preg_replace('/[^0-9]/', '', $item['price']);
                                    $tong_bill += ($p_price * (int)$item['quantity']);
                                ?>
                                    <li><?php echo htmlspecialchars($item['product_name']); ?> <b>x<?php echo $item['quantity']; ?></b></li>
                                <?php endwhile; ?>
                                
                                <?php if(!$has_items): ?>
                                    <li style="color:red; list-style:none; margin-left:-15px;"><i>(Chưa có chi tiết sản phẩm)</i></li>
                                <?php endif; ?>
                            </ul>
                        </td>
                        
                        <td style="color:#d32f2f; font-weight:bold; font-size: 15px;">
                            <?php 
                                $display_money = ($tong_bill > 0) ? $tong_bill : $row['total_money'];
                                echo number_format($display_money, 0, ',', '.'); 
                            ?>đ
                        </td>
                        
                        <td>
                            <?php 
                            $status = $row['status'];
                            if($status == 'Chờ duyệt') echo '<span class="badge bg-pending">Chờ duyệt</span>';
                            elseif($status == 'Đã thanh toán') echo '<span class="badge bg-paid">Đã thanh toán</span>';
                            elseif($status == 'Đã vận chuyển') echo '<span class="badge bg-shipped">Đã vận chuyển</span>';
                            else echo '<span class="badge" style="background:#999">'.htmlspecialchars($status).'</span>';
                            ?>
                        </td>

                        <td>
                            <?php if($status == 'Chờ duyệt'): ?>
                                <a href="admin_orders.php?action=approve&id=<?php echo $oid; ?>" 
                                   class="btn-action btn-approve"
                                   onclick="return confirm('Duyệt đơn hàng #<?php echo $oid; ?>?')">
                                   <i class="fas fa-check"></i> Duyệt
                                </a>
                            <?php elseif($status == 'Đã thanh toán'): ?>
                                <a href="admin_orders.php?action=ship&id=<?php echo $oid; ?>" 
                                   class="btn-action btn-ship"
                                   onclick="return confirm('Giao hàng đơn #<?php echo $oid; ?>?')">
                                   <i class="fas fa-truck"></i> Vận chuyển
                                </a>
                            <?php elseif($status == 'Đã vận chuyển'): ?>
                                <span style="color:#2980b9; font-weight:bold;">
                                     <i class="fas fa-check-circle"></i> Đã giao đi
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 40px;'>Chưa có đơn hàng nào!</td></tr>";
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>