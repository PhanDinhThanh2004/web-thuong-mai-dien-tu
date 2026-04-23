<?php
session_start();
require_once 'db.php';

$error = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine if it's Login or Register
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                $user_data = json_encode([
                    'username' => $row['username'],
                    'role' => $row['role']
                ]);

                // Redirect based on role
                $target = ($row['role'] == 1) ? 'admin/index.php' : 'index.html';
                echo "<script>
                    localStorage.setItem('currentUser', '$user_data');
                    window.location.href = '$target';
                </script>";
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tài khoản không tồn tại!";
        }
    } elseif ($action === 'register') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Mật khẩu xác nhận không khớp!";
        } else {
            // Check if username already exists
            $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Tên đăng nhập đã tồn tại!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    $success_message = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";
                } else {
                    $error = "Đã xảy ra lỗi: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký | TimeHouse</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6600;
            --primary-dark: #e65c00;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-light: #ffffff;
            --text-dark: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/auth-bg.png') no-repeat center center/cover;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 450px;
            max-width: 90%;
            height: 600px;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 25px 45px rgba(0,0,0,0.2);
            padding: 40px;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.5s ease-in-out;
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        .form-group input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .form-group input:focus {
            background: rgba(255,255,255,0.2);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(255, 102, 0, 0.3);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            text-transform: uppercase;
        }

        button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.4);
        }

        .toggle-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.95rem;
        }

        .toggle-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .toggle-link a:hover {
            text-decoration: underline;
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0.8;
            transition: 0.3s;
        }

        .back-home:hover {
            opacity: 1;
            color: var(--primary);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        .alert-error {
            background: rgba(211, 47, 47, 0.2);
            border: 1px solid rgba(211, 47, 47, 0.5);
            color: #ff8a80;
        }

        .alert-success {
            background: rgba(56, 142, 60, 0.2);
            border: 1px solid rgba(56, 142, 60, 0.5);
            color: #a5d6a7;
        }

        /* Logic to hide/show forms */
        #register-form { display: none; }
        .show-register #login-form { display: none; }
        .show-register #register-form { display: block; }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="<?php echo ($action === 'register' || !empty($success_message)) ? 'show-register' : ''; ?>">

    <a href="index.html" class="back-home">← Về trang chủ</a>

    <div class="container fade-in">
        <!-- Login Form -->
        <div id="login-form">
            <h2>Đăng Nhập</h2>
            <?php if($error && $action === 'login'): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Tên đăng nhập" required autocomplete="username">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required autocomplete="current-password">
                </div>
                <button type="submit">Đăng Nhập</button>
            </form>
            <div class="toggle-link">
                Chưa có tài khoản? <a onclick="toggleForm()">Đăng ký ngay</a>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form">
            <h2>Đăng Ký</h2>
            <?php if($error && $action === 'register'): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php elseif($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Tên đăng nhập" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Địa chỉ email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                </div>
                <button type="submit">Đăng Ký</button>
            </form>
            <div class="toggle-link">
                Đã có tài khoản? <a onclick="toggleForm()">Đăng nhập ngay</a>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            document.body.classList.toggle('show-register');
            // Change title dynamically
            const isRegister = document.body.classList.contains('show-register');
            document.title = isRegister ? "Đăng Ký | TimeHouse" : "Đăng Nhập | TimeHouse";
        }
    </script>
</body>
</html>
