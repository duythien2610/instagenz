<?php
// 1. Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Nhúng file chứa hàm (Quan trọng: Sửa đường dẫn đúng với thư mục của bạn)
// Nếu bạn có file functions.php, hãy bỏ comment dòng dưới:
// require_once 'assets/php/functions.php'; 

global $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Instagenz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Grand+Hotel&display=swap" rel="stylesheet">
    
    <style>
        /* (Giữ nguyên CSS cũ của bạn ở đây) */
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .main-container { width: 100%; max-width: 350px; }
        .card-custom { background-color: #fff; border: 1px solid #dbdbdb; padding: 40px 30px; margin-bottom: 10px; text-align: center; }
        .logo-text { font-family: 'Grand Hotel', cursive; font-size: 3rem; margin-bottom: 20px; color: #262626; }
        .info-text { color: #8e8e8e; font-size: 14px; margin-bottom: 20px; line-height: 1.4; }
        .email-highlight { color: #262626; font-weight: 600; }
        .form-control { background-color: #fafafa; border: 1px solid #dbdbdb; border-radius: 3px; font-size: 14px; padding: 9px 8px; margin-bottom: 10px; text-align: center; letter-spacing: 2px; }
        .form-control:focus { background-color: #fff; border-color: #a8a8a8; box-shadow: none; }
        .btn-primary { background-color: #0095f6; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; padding: 7px 16px; width: 100%; margin-top: 10px; }
        .btn-primary:hover { background-color: #1877f2; }
        .resend-link { color: #0095f6; font-size: 13px; font-weight: 600; text-decoration: none; margin-top: 15px; display: inline-block; }
        .logout-box { background-color: #fff; border: 1px solid #dbdbdb; padding: 20px; text-align: center; font-size: 14px; }
        .logout-link { color: #ed4956; text-decoration: none; font-weight: 600; }
        input:focus::placeholder { color: transparent; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="card-custom">
        <img src="assets/images/instagenz.png" alt="Logo" style="height: 100px; width: auto; object-fit: contain;">
        <div class="mb-3">
            <i class="bi bi-envelope-paper" style="font-size: 3rem; color: #262626;"></i>
        </div>
        
        <h6 class="mb-2 fw-bold">Enter Confirmation Code</h6>
        <p class="info-text">
            Enter the 6-digit code we sent to <br>
            <span class="email-highlight"><?= $_SESSION['verification_email'] ?? 'your-email@example.com' ?></span>
        </p>

        <form method="post" action="actions.php?verify_email">
            <div class="mb-3">
                <input type="text" name="code" class="form-control" placeholder="######" maxlength="6" required autocomplete="off">
            </div>

            <?php if (isset($_GET['resended'])) { ?>
                <p class="text-success small fw-bold">Code has been resent!</p>
            <?php } ?>
            
            <?php 
            if (function_exists('showError')) {
                showError('email_verify');
            } else {
                // Nếu chưa có hàm thì hiện lỗi mặc định của PHP nếu có biến lỗi
                if(isset($_SESSION['error']['email_verify'])){
                    echo '<div class="text-danger small mb-2">'.$_SESSION['error']['email_verify'].'</div>';
                    unset($_SESSION['error']['email_verify']); // Xóa lỗi sau khi hiện
                }
            }
            ?>

            <button class="btn btn-primary" type="submit">Verify Email</button>
            <br>
            <a href="actions.php?resend_code" class="resend-link">Resend Code</a>
        </form>
    </div>

    <div class="logout-box">
        Wrong email? <a href="actions.php?logout" class="logout-link">Log out</a>
    </div>
</div>

</body>
</html>