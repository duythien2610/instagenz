<?php
// includes/send_mail.php

// Gọi thư viện PHPMailer từ folder libs
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

// Hàm gửi mã OTP
function sendOTP($email, $subject, $code) {
    $mail = new PHPMailer(true);

    try {
        // 1. Cấu hình Server (Gmail SMTP)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- ĐIỀN THÔNG TIN CỦA BẠN VÀO ĐÂY ---
        $mail->Username   = 'doduythien261005@gmail.com'; // Email của bạn
        $mail->Password   = 'ahsm xtef hzoq ibho';        // Mật khẩu ứng dụng (App Password)
        // ----------------------------------------
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // 2. Cấu hình người gửi/nhận
        $mail->setFrom('doduythien261005@gmail.com', 'InstaGenz Support');
        $mail->addAddress($email);

        // 3. Nội dung Email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                <h2>Xin chào,</h2>
                <p>Mã xác thực của bạn là: <b style='font-size: 20px; color: #0d6efd;'>$code</b></p>
                <p>Vui lòng nhập mã này để tiếp tục. Mã có hiệu lực trong 5 phút.</p>
                <hr>
                <small>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email.</small>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Ghi log lỗi nếu cần, nhưng trả về false để logic chính biết là gửi thất bại
        return false;
    }
}
?>