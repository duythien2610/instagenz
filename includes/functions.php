<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once 'db.php';

function showPage($page, $data =[]){
    $path = "pages/$page.php";
    if(file_exists($path)){
        include($path);
    }else{
        echo"Lỗi: Không tìm thấy file giao diện [$path]";
    }
}

function showError($field){
    if(isset($_SESSION['error'])){
        $error = $_SESSION['error'];
        if(isset($error['field']) && $field == $error['field']){
            ?>
            <div class="text-danger my-1" style="font-size: small;">
                <?= $error['msg'] ?>
            </div>
            <?php
        }
    }
}

function showFormData($field){
    if(isset($_SESSION['formdata']) && isset($_SESSION['formdata'][$field])){
        return $_SESSION['formdata'][$field];
    }
    return '';
}

// includes/functions.php

// Hàm chuyển đổi thời gian sang dạng "Time Ago"
function show_time_ago($time){
    // Chuyển đổi thời gian từ DB thành Timestamp
    $time_ago = strtotime($time);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    // Tính toán các đơn vị
    $minutes      = round($seconds / 60 );           // 60 giây
    $hours        = round($seconds / 3600);          // 60 phút
    $days         = round($seconds / 86400);         // 24 giờ
    $weeks        = round($seconds / 604800);        // 7 ngày
    $months       = round($seconds / 2629440);       // 30.4 ngày
    $years        = round($seconds / 31553280);      // 365.25 ngày

    if($seconds <= 60){
        return "Vừa xong";
    } else if($minutes <= 60){
        if($minutes == 1){
            return "1 phút trước";
        } else {
            return "$minutes phút trước";
        }
    } else if($hours <= 24){
        if($hours == 1){
            return "1 giờ trước";
        } else {
            return "$hours giờ trước";
        }
    } else if($days <= 7){
        if($days == 1){
            return "Hôm qua";
        } else {
            return "$days ngày trước";
        }
    } else if($weeks <= 4.3){ // 4.3 == 30/7
        if($weeks == 1){
            return "1 tuần trước";
        } else {
            return "$weeks tuần trước";
        }
    } else if($months <= 12){
        if($months == 1){
            return "1 tháng trước";
        } else {
            return "$months tháng trước";
        }
    } else {
        if($years == 1){
            return "1 năm trước";
        } else {
            return "$years năm trước";
        }
    }
}

// Hàm gửi thông báo
function sendNotification($from_user_id, $to_user_id, $post_id, $message){
    global $db;
    
    // Không bao giờ tự thông báo cho chính mình
    if($from_user_id == $to_user_id){
        return;
    }

    $created_at = date('Y-m-d H:i:s');
    
    // Chèn vào DB
    $sql = "INSERT INTO notifications(to_user_id, from_user_id, post_id, message, created_at) 
            VALUES($to_user_id, $from_user_id, $post_id, '$message', '$created_at')";
    
    mysqli_query($db, $sql);
}

// Hàm lấy danh sách thông báo
function getNotifications($user_id){
    global $db;
    // Lấy thông báo mới nhất lên đầu, join với bảng users để lấy avatar người gửi
    $sql = "SELECT n.*, u.username, u.first_name, u.last_name, u.profile_pic 
            FROM notifications n 
            JOIN users u ON n.from_user_id = u.id 
            WHERE n.to_user_id = $user_id 
            ORDER BY n.id DESC";
    $run = mysqli_query($db, $sql);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

// Hàm đếm thông báo chưa đọc (để hiện số đỏ trên cái chuông)
function countUnreadNotifications($user_id){
    global $db;
    $sql = "SELECT * FROM notifications WHERE to_user_id = $user_id AND read_status = 0";
    $run = mysqli_query($db, $sql);
    return mysqli_num_rows($run);
}