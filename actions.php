<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/user.php';
require_once 'includes/send_mail.php';

// Xử lý đăng ký
if(isset($_GET['signup'])){
    $response = validateSignupForm($_POST);
    if($response['status']){
        if(createUser($_POST)){
            $otp = rand(100000, 999999);
            $_SESSION['verification_otp'] = $otp;
            $_SESSION['verification_email'] = $_POST['email'];
            //Gửi amil
            sendOTP($_POST['email'], 'Verify account with code', $otp);
            header('location: index.php?verify_email');
        }else{
            echo"<script>alert('Có lỗi xảy ra !')</script>";
        }
    }else{
        $_SESSION['error'] = $response;
        $_SESSION['formdata'] = $_POST;
        header("location: index.php?signup");
    }
}
//Xử lý xác thực email
if(isset($_GET['verify_email'])){
    $user_code = $_POST['code'];
    $server_code = $_SESSION['verification_otp'] ?? null;

    if($user_code == $server_code){
        verifyEmail($_SESSION['verification_email']);

        unset($_SESSION['verification_otp']);
        unset($_SESSION['verification_email']);

    header('location: index.php?login&verified');
    exit;
    }else{
        $response['msg'] = "Mã xác thực không đúng";
        $response['field'] = 'email_verify';
        $_SESSION['error'] = $response;
        header('location: index.php?verify_email');
    }

}

//Xử lý gửi lại mã
if(isset($_GET['resend_code'])){
    if(isset($_SESSION['verification_email'])){
        $otp = rand(100000,999999);
        $_SESSION['verification_otp'] = $otp;
        sendOTP($_SESSION['verification_email'], 'Resend Your OTP', $otp);
        header('location: index.php?verify_email&resended');
    }else{
        header('location: index.php?login');
    }
}
//Xử lý đăng nhập
if(isset($_GET['login'])){
    $response = array();
    if(!$_POST['password'] || !$_POST['username_email']){
        $response['msg'] = "Vui lòng điền đầy đủ thông tin";
        $response['field'] = 'checkuser';
        $_SESSION['error'] = $response;
        header("location: index.php?login");
        exit;
    }
    $check = checkUser($_POST);
    if($check['status']){
        $_SESSION['Auth'] = true;
        $_SESSION['userdata'] = $check['user'];
        header("location: index.php");
    }else{
        $response['msg'] = $check['msg'] ?? "Sai tài khoản hoặc mật khẩu";
        $response['field'] = 'checkuser';
        $_SESSION['error'] = $response;
        $_SESSION['formdata'] = $_POST;
        header("location: index.php?login");
    }
}

//Xử lý quên mật khẩu
if(isset($_GET['forgotpassword'])){
    $response = array();
    $email = $_POST['email'];
    if(checkEmailExist($email)){
        $otp = rand(100000,999999);
        $_SESSION['forgot_otp'] = $otp;
        $_SESSION['forgot_email'] = $email;
    
        sendOTP($email, 'Recovery Your Password', $otp);
        header('location: index.php?forgotpassword&verify');
    }else{
        $response['msg'] = "Email này chưa được đăng ký";
        $response['field'] = 'email';
        $_SESSION['error'] = $response;
        header('location: index.php?forgotpassword');
    }
}
//Verify OTP mật khẩu
if(isset($_GET['verify_forgot_code'])){
    $user_code = $_POST['code'];
    $server_code = $_SESSION['forgot_otp'] ?? null;
    if($user_code == $server_code){
        $_SESSION['auth_temp'] = true;
        header('location: index.php?forgotpassword&changepass');
    }else{
        $response['msg'] = 'Mã không đúng';
        $response['field'] = 'email_verify';
        $_SESSION['error'] = $response;
        header('location: index.php?forgotpassword&verify');
    }
}
//Đổi mật khẩu mới
if(isset($_GET['changepassword'])){
    if(!isset($_SESSION['auth_temp'])){
        header('location: index.php?login');
        exit;
    }
    if($_POST['password']){
        resetPassword($_SESSION['forgot_email'], $_POST['password']);
        unset($_SESSION['forgot_email']);
        unset($_SESSION['forgot_otp']);
        unset($_SESSION['auth_temp']);
        header('location: index.php?login&changed_pass');
    }else{
        $response['msg'] = "Vui lòng nhập mật khẩu mới";
        $response['field'] = 'password';
        $_SESSION['error'] = $response;
        header('location: index.php?forgotpassword&changepass');
    }
}
// actions.php

// XỬ LÝ CẬP NHẬT PROFILE
if(isset($_GET['update_profile'])){
    
    if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['username'])){
        $response['msg'] = "Vui lòng điền đầy đủ thông tin";
        $response['status'] = false;
        $response['field'] = 'first_name'; 
        $_SESSION['error'] = $response;
        header("location: index.php?editprofile");
        exit;
    }

    // 2. Gọi hàm update (Truyền $_FILES để xử lý ảnh)
    if(updateUserProfile($_POST, $_FILES['profile_pic'])){
        

        $new_user_info = getUser($_SESSION['userdata']['id']);
        $_SESSION['userdata'] = $new_user_info;

        header("location: index.php?editprofile&success");
    } else {
        echo "<script>alert('Lỗi: Không thể lưu vào Database')</script>";
    }
}
// XỬ LÝ ĐĂNG BÀI VIẾT
if(isset($_GET['add_post'])){
    $response = validateSignupForm($_POST); 

    
    if($_POST['post_text'] || $_FILES['post_img']['name']){
        if(createPost($_POST['post_text'], $_FILES['post_img'])){
            header("location: index.php");
        }else{
            echo "<script>alert('Lỗi đăng bài')</script>";
        }
    }else{

        header("location: index.php?msg=empty_post");
    }
}
//xử lý đăng xuất
if(isset($_GET['logout'])){
    session_destroy();
    header('location: index.php?login');
}