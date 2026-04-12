<?php
session_start();
include 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            // Lưu session server
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // --- QUAN TRỌNG: Lưu thêm 'role' vào localStorage để JS dùng ---
            $user_data = json_encode([
                'username' => $row['username'],
                'role' => $row['role'] // Thêm dòng này
            ]);
            
            if ($row['role'] == 1) {
                echo "<script>
                    localStorage.setItem('currentUser', '$user_data');
                    window.location.href = 'admin/index.php';
                </script>";
            } else {
                echo "<script>
                    localStorage.setItem('currentUser', '$user_data');
                    window.location.href = 'sanpham.php';
                </script>";
            }
            exit();
        } else {
            $error = "Mật khẩu sai!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập | TimeHouse</title>
    <style>body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;background:#f9f9f9}.box{background:#fff;padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);width:350px;text-align:center}input{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd}button{width:100%;padding:10px;background:#ff6600;color:#fff;border:none;cursor:pointer}.error{color:red}</style>
</head>
<body>
    <div class="box">
        <h2>Đăng Nhập</h2>
        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng Nhập</button>
        </form>
        <p><a href="dangky.php">Đăng ký</a> | <a href="index.html">Về trang chủ</a></p>
    </div>
</body>
</html>
