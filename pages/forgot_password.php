<?php
// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
global $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Instagenz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Grand+Hotel&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .main-container {
            width: 100%;
            max-width: 380px; /* Rộng hơn xíu cho thoáng */
        }

        .card-custom {
            background-color: #fff;
            border: 1px solid #dbdbdb;
            padding: 30px 40px;
            margin-bottom: 10px;
            text-align: center;
        }

        .logo-text {
            font-family: 'Grand Hotel', cursive;
            font-size: 3rem;
            margin-bottom: 10px;
            color: #262626;
        }

        /* Icon khóa tròn đặc trưng */
        .lock-icon-circle {
            width: 90px;
            height: 90px;
            border: 2px solid #262626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }
        
        .lock-icon-circle i {
            font-size: 45px;
            color: #262626;
        }

        h6 {
            font-weight: 600;
            color: #262626;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info-text {
            color: #8e8e8e;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .form-control {
            background-color: #fafafa;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            font-size: 14px;
            padding: 9px 8px;
            margin-bottom: 10px;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #a8a8a8;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #0095f6;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #1877f2;
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #8e8e8e;
            font-size: 13px;
            font-weight: 600;
            margin: 20px 0;
        }
        .separator::before, .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dbdbdb;
        }
        .separator::before { margin-right: 15px; }
        .separator::after { margin-left: 15px; }

        .back-login-box {
            background-color: #fff;
            border: 1px solid #dbdbdb;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        .back-login-link {
            color: #262626;
            text-decoration: none;
        }
        
        /* Ẩn placeholder khi focus */
        input:focus::placeholder {
            color: transparent;
        }
        
        /* Text đỏ báo lỗi */
        .error-msg {
            color: #ed4956;
            font-size: 12px;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="card-custom">
        <img src="assets/images/instagenz.png" alt="Logo" style="height: 100px; width: auto; object-fit: contain;">

        <?php
        // --- TRƯỜNG HỢP 1: NHẬP MÃ OTP ---
        if (isset($_GET['verify'])):
        ?>
            <div class="lock-icon-circle">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h6>Verify OTP</h6>
            <p class="info-text">
                Enter the 6-digit code sent to your email:<br>
                <strong><?= $_SESSION['forgot_email'] ?? 'Unknown Email' ?></strong>
            </p>

            <form method="post" action="actions.php?verify_forgot_code">
                <input type="text" name="code" class="form-control text-center" placeholder="######" maxlength="6" required>
                
                <?php if(function_exists('showError')) showError('email_verify'); ?>

                <button class="btn btn-primary" type="submit">Verify Code</button>
                
                <div class="separator">OR</div>
                <a href="index.php?forgotpassword" class="text-decoration-none fw-bold" style="font-size: 14px; color: #262626;">Back to Email</a>
            </form>

        <?php
        // --- TRƯỜNG HỢP 2: ĐỔI MẬT KHẨU MỚI ---
        elseif (isset($_GET['changepass'])):
        ?>
            <div class="lock-icon-circle" style="border-color: #0095f6;">
                <i class="bi bi-key" style="color: #0095f6;"></i>
            </div>
            <h6>Create New Password</h6>
            <p class="info-text">Your password must be strong and hard to guess.</p>

            <form method="post" action="actions.php?changepassword">
                <input type="password" name="password" class="form-control" placeholder="New Password" required>
                
                <?php if(function_exists('showError')) showError('password'); ?>

                <button class="btn btn-primary" type="submit">Reset Password</button>
            </form>

        <?php
        // --- TRƯỜNG HỢP 3: NHẬP EMAIL (MẶC ĐỊNH) ---
        else:
        ?>
            <div class="lock-icon-circle">
                <i class="bi bi-lock"></i>
            </div>
            <h6>Trouble Logging In?</h6>
            <p class="info-text">Enter your email and we'll send you an OTP to get back into your account.</p>

            <form method="post" action="actions.php?forgotpassword">
                <input type="email" name="email" class="form-control" placeholder="Email address" required>
                
                <?php if(function_exists('showError')) showError('email'); ?>

                <button class="btn btn-primary" type="submit">Send Login Link</button>
            </form>
            
            <div class="separator">OR</div>
            <a href="signup.php" class="text-decoration-none fw-bold" style="font-size: 14px; color: #262626;">Create New Account</a>

        <?php endif; ?>
    </div>

    <?php if (!isset($_GET['changepass'])): ?>
    <div class="back-login-box">
        <a href="?login" class="back-login-link">Back to Login</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>