<?php
include 'db.php'; 
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) { $message = "Tên đăng nhập đã tồn tại!"; }
    else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        if (mysqli_query($conn, $sql)) { echo "<script>alert('Đăng ký thành công!'); window.location.href='dangnhap.php';</script>"; }
        else { $message = "Lỗi: " . mysqli_error($conn); }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><title>Đăng Ký</title><style>body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;background:#f9f9f9}.box{background:#fff;padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);width:350px;text-align:center}input{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd}button{width:100%;padding:10px;background:#ff6600;color:#fff;border:none;cursor:pointer}.error{color:red}</style></head>
<body>
    <div class="box">
        <h2>Đăng Ký</h2>
        <?php if($message) echo "<div class='error'>$message</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng Ký</button>
        </form>
        <p><a href="dangnhap.php">Đăng nhập</a> | <a href="index.html">Về trang chủ</a></p>
    </div>
</body>
</html>
