<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Hàm này giúp lọc sạch số. Ví dụ: "5.000.000đ" -> thành số 5000000
function getAmount($str) {
    if (!$str) return 0;
    // Chuyển thành chuỗi
    $str = (string)$str;
    // Chỉ giữ lại số 0-9, xóa hết dấu chấm, phẩy, chữ
    $clean = preg_replace('/[^0-9]/', '', $str);
    // Trả về số nguyên
    return (int)$clean;
}

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
    exit;
}
$user_id = (int)$_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

// 2. LẤY DANH SÁCH (Sửa lỗi hiển thị giá)
if ($action == 'list') {
    $sql = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = $user_id";
    $result = mysqli_query($conn, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // --- QUAN TRỌNG: Dùng hàm getAmount để lấy đúng giá ---
        $row['price'] = getAmount($row['price']); 
        $data[] = $row;
    }
    echo json_encode($data);
}

// 3. THÊM VÀO GIỎ
elseif ($action == 'add') {
    $p_id = (int)$_POST['product_id'];
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$p_id");
    if(mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity=quantity+1 WHERE user_id=$user_id AND product_id=$p_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $p_id, 1)");
    }
    echo json_encode(['status'=>'success', 'message'=>'Đã thêm vào giỏ!']);
}

// 4. CẬP NHẬT SỐ LƯỢNG
elseif ($action == 'update') {
    $id = (int)$_POST['cart_id'];
    $qty = (int)$_POST['change'];
    mysqli_query($conn, "UPDATE cart SET quantity=quantity+$qty WHERE id=$id");
    mysqli_query($conn, "DELETE FROM cart WHERE quantity<=0"); 
    echo json_encode(['status'=>'success']);
}

// 5. XÓA SẢN PHẨM
elseif ($action == 'remove') {
    $id = (int)$_POST['cart_id'];
    mysqli_query($conn, "DELETE FROM cart WHERE id=$id");
    echo json_encode(['status'=>'success']);
}

// 6. THANH TOÁN (Sửa lỗi tính tổng tiền sai khi lưu vào DB)
elseif ($action == 'checkout') {
    $cart_q = mysqli_query($conn, "SELECT c.*, p.price, p.name FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
    
    $items = []; 
    $total_money = 0;
    
    while($r = mysqli_fetch_assoc($cart_q)) {
        // --- QUAN TRỌNG: Lọc số trước khi tính toán ---
        $price_val = getAmount($r['price']); 
        $qty_val = (int)$r['quantity'];
        
        $r['price'] = $price_val; // Lưu lại giá sạch vào mảng
        $items[] = $r;
        
        // Tính tổng: Số nhân Số (Chắc chắn đúng)
        $total_money += ($price_val * $qty_val);
    }

    if (empty($items)) {
        echo json_encode(['status'=>'error', 'message'=>'Giỏ hàng trống!']); exit;
    }

    $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
    $name = $u['fullname'] ?? 'Khách'; $phone = $u['phone'] ?? ''; $addr = $u['address'] ?? '';

    // Tạo đơn hàng (Trạng thái: Chờ duyệt)
    $sql = "INSERT INTO orders (user_id, fullname, phone, address, total_money, status, created_at) 
            VALUES ($user_id, '$name', '$phone', '$addr', $total_money, 'Chờ duyệt', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $oid = mysqli_insert_id($conn);
        foreach ($items as $item) {
            $pname = addslashes($item['name']);
            // Lưu giá sạch vào DB
            mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                                 VALUES ($oid, {$item['product_id']}, '$pname', {$item['price']}, {$item['quantity']})");
        }
        mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");
        echo json_encode(['status'=>'success', 'message'=>'Đặt hàng thành công!']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Lỗi hệ thống: '.mysqli_error($conn)]);
    }
}
?>