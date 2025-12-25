<?php
//Bật hiển thị lỗi
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

require_once 'includes/functions.php';
require_once 'includes/user.php';

$page ='login';
$page_title = 'InstaGenz - Đăng nhập';

// Mặc định nếu đã đăng nhập thì vào Wall
if(isset($_SESSION['Auth'])){
    $page = 'wall';
    $page_title = 'Trang chủ';
    $posts = filterPosts();
}



if(isset($_GET['signup'])){
    $page = 'signup';
    $page_title = 'Đăng ký thành viên';
}
elseif(isset($_GET['login'])){
    $page = 'login';
    $page_title = 'Đăng nhập';
}
elseif(isset($_GET['verify_email'])){
    $page = 'verify_email';
    $page_title = 'Xác thực tài khoản';
}
elseif(isset($_SESSION['Auth']) && isset($_GET['editprofile'])){
    $page = 'edit_profile';
    $page_title = 'Chỉnh sửa hồ sơ';
}
elseif(isset($_SESSION['Auth']) && isset($_GET['search'])){
    $page = 'search';
    $page_title = 'Kết quả tìm kiếm';
}
elseif(isset($_SESSION['Auth']) && isset($_GET['u'])){
    $page = 'profile';
    $page_title = 'Trang cá nhân';
}
// ------------------------------
elseif(isset($_GET['forgotpassword'])){
    $page = 'forgot_password';
    $page_title = 'Quên mật khẩu';
}



showPage('header', ['page_title'=>$page_title]);

if(isset($_SESSION['Auth'])){
    showPage('navbar');
}

showPage($page); // Hàm này sẽ tự động gọi file pages/$page.php (tức là pages/profile.php)

showPage('footer');

// Xóa session lỗi/form data sau khi hiển thị xong
if(isset($_SESSION['error'])){
    unset($_SESSION['error']);
}
if(isset($_SESSION['formdata'])){
    unset($_SESSION['formdata']);
}
?>