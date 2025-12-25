<?php
require_once 'db.php';

//Tạo user mới đăng ký
function createUser($data){
    global $db;

    $first_name = mysqli_real_escape_string($db, $data['first_name']);
    $last_name = mysqli_real_escape_string($db, $data['last_name']);
    $gender = (int)$data['gender'];
    $email = mysqli_real_escape_string($db, $data['email']);
    $username = mysqli_real_escape_string($db, $data['username']);
    $password = mysqli_real_escape_string($db, $data['password']);

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $current_status = checkUserStatusByEmail($email);
    if ($current_status === null) {
        // User mới tinh -> INSERT (Thêm NOW() cho created_at)
        $query = "INSERT INTO users(first_name, last_name, gender, email, username, password, ac_status, created_at, updated_at) ";
        $query .= "VALUES ('$first_name', '$last_name', '$gender', '$email', '$username', '$password_hashed', 0, NOW(), NOW())";
    } else {
        // User cũ chưa kích hoạt -> UPDATE lại thông tin
        $query = "UPDATE users SET 
                    first_name='$first_name', 
                    last_name='$last_name', 
                    gender='$gender', 
                    username='$username', 
                    password='$password_hashed', 
                    updated_at=NOW() 
                  WHERE email='$email'";
    }
    return mysqli_query($db, $query);
}
// includes/user.php

// Hàm lấy thông tin 1 user theo ID
function getUser($user_id){
    global $db;
    $query = "SELECT * FROM users WHERE id='$user_id'";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run);
}
function getUserByUsername($username){
    global $db;
    $query = "SELECT * FROM users WHERE username='$username'";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run);
}


function validateSignupForm($form_data){
    $response = array();
    $response['status'] = true;

    if (empty($form_data['first_name'])) {
        $response['msg'] = "Bạn quên nhập Họ rồi";
        $response['status'] = false;
        $response['field'] = 'first_name'; 
        return $response;
    }
    if (empty($form_data['last_name'])) {
        $response['msg'] = "Bạn quên nhập Tên rồi";
        $response['status'] = false;
        $response['field'] = 'last_name';
        return $response;
    }
    if (empty($form_data['email'])) {
        $response['msg'] = "Email không được để trống";
        $response['status'] = false;
        $response['field'] = 'email';
        return $response;
    }

    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $response['msg'] = "Định dạng email không hợp lệ";
        $response['status'] = false;
        $response['field'] = 'email';
        return $response;
    }
    $email_status = checkUserStatusByEmail($form_data['email']);
    if($email_status===1){
        $response['msg'] = "Email này đã được đăng ký và kích hoạt";
        $response['status'] = false;
        $response['field'] = 'email';
        return $response;
    }
    if(empty($form_data['username'])){
        $response['msg'] = "Vui lòng nhập username";
        $response['status'] = false;
        $response['field'] = 'username';
        return $response;
    }
    if (checkUsernameExist($form_data['username'])) {
        $response['msg'] = "Username này đã có người dùng";
        $response['status'] = false;
        $response['field'] = 'username';
        return $response;
    }
    if(empty($form_data['password'])){
        $response['msg'] = "Vui lòng nhập mật khẩu";
        $response['status'] = false;
        $response['field'] = 'password';
        return $response;
    }
    if (strlen($form_data['password']) < 6) {
        $response['msg'] = "Mật khẩu phải từ 6 ký tự trở lên";
        $response['status'] = false;
        $response['field'] = 'password';
        return $response;
    }
    return $response;
}
function checkUsernameExist($username) {
    global $db;
    $username = mysqli_real_escape_string($db, $username);
    $query = "SELECT count(*) as count FROM users WHERE username='$username'";
    $run = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($run);
    return $data['count'] > 0; 
}


function checkEmailExist($email) {
    global $db;
    $email = mysqli_real_escape_string($db, $email);
    $query = "SELECT count(*) as count FROM users WHERE email='$email'";
    $run = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($run);
    return $data['count'] > 0;
}
function checkUser($login_data){
    global $db;
    $username_email = mysqli_real_escape_string($db, $login_data['username_email']);
    $password = $login_data['password'];

    $query = "SELECT * FROM users WHERE username = '$username_email' OR email = '$username_email'";
    $run = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($run);

    $data['user'] = $user;
    $data['status'] = false;

    if($user){
        // 1. Kiểm tra mật khẩu trước
        if(password_verify($password, $user['password'])){
            
            // 2. Mật khẩu đúng rồi -> Kiểm tra trạng thái tài khoản
            if($user['ac_status'] == 0){
                $data['msg'] = "Tài khoản chưa xác thực email. Vui lòng kiểm tra hộp thư.";
            }
            elseif($user['ac_status'] == 1){
                $data['status'] = true; // Chỉ có 1 là được vào
            }
            elseif($user['ac_status'] == 2){
                $data['msg'] = "Tài khoản của bạn đã bị KHÓA bởi Admin!";
            }
            else {
                $data['msg'] = "Lỗi trạng thái tài khoản không xác định.";
            }

        } else {
            $data['msg'] = "Sai mật khẩu";
        }
    } else {
        $data['msg'] = "Tài khoản không tồn tại";
    }

    return $data;
}

function verifyEmail($email){
    global $db;
    $email = mysqli_real_escape_string($db,$email);
    $query = "UPDATE users SET ac_status = 1 WHERE email = '$email'";
    return mysqli_query($db, $query); 
}
function resetPassword($email, $password){
    global $db;
    $email = mysqli_real_escape_string($db, $email);
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = '$password_hashed' WHERE email = '$email'";
    return mysqli_query($db, $query);
}

function checkUserStatusByEmail($email){
    global $db;
    $email = mysqli_real_escape_string($db,$email);
    $query = "SELECT ac_status FROM users WHERE email='$email'";
    $run = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($run);

    if(!$data) return null;

    return $data['ac_status'];
}

function updateUserProfile($data, $imagedata){
    global $db;
    $first_name = mysqli_real_escape_string($db, $data['first_name']);
    $last_name= mysqli_real_escape_string($db, $data['last_name']);
    $username = mysqli_real_escape_string($db, $data['username']);
    $password = mysqli_real_escape_string($db, $data['password']);

    if($imagedata['name']){
        $image_name = time() . '_' . $imagedata['name'];
        move_uploaded_file($imagedata['tmp_name'], 'assets/images/profile/'.$image_name);
        $profile_pic = $image_name;
    }else{
        $profile_pic = $_SESSION['userdata']['profile_pic'];
    }

    if($password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET 
                  first_name='$first_name', 
                  last_name='$last_name', 
                  username='$username', 
                  password='$hashed_password', 
                  profile_pic='$profile_pic', 
                  updated_at=NOW() 
                  WHERE id=".$_SESSION['userdata']['id'];
    } else { 
        $query = "UPDATE users SET 
                  first_name='$first_name', 
                  last_name='$last_name', 
                  username='$username', 
                  profile_pic='$profile_pic', 
                  updated_at=NOW() 
                  WHERE id=".$_SESSION['userdata']['id'];
    }
    return mysqli_query($db, $query);
}

//QUẢN LÝ POST

function createPost($text, $image){
    global $db;
    $user_id = $_SESSION['userdata']['id'];
    $post_text = mysqli_real_escape_string($db, $text);
    $post_img ='';
    if($image['name']){
        $image_name = time() . '_' . $image['name'];
        move_uploaded_file($image['tmp_name'], 'assets/images/posts/'.$image_name);
        $post_img = $image_name;
    }
    $query = "INSERT INTO posts(user_id, post_text, post_img) VALUES($user_id, '$post_text', '$post_img')";
    return mysqli_query($db, $query);
}

function getPosts(){
    global $db;
    $query="SELECT p.id, p.user_id, p.post_text, p.post_img, p.created_at, 
                     u.first_name, u.last_name, u.username, u.profile_pic 
              FROM posts p 
              JOIN users u ON u.id = p.user_id 
              ORDER BY p.id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

// 2. Lấy bài viết của một user cụ thể
function getPostById($user_id){
    global $db;
    $query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}
function filterPosts(){
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];

    $query = "SELECT posts.*, u.first_name, u.last_name, u.username, u.profile_pic 
              FROM posts 
              JOIN users u ON posts.user_id = u.id 
              WHERE posts.user_id IN (
                  SELECT user_id FROM follow_list WHERE follower_id = $current_user_id
              ) 
              OR posts.user_id = $current_user_id 
              ORDER BY posts.id DESC";

    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}
//Check like status
function checkLikeStatus($post_id){
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];
    $query = "SELECT count(*) as count FROM likes WHERE user_id=$current_user_id AND post_id = $post_id";
    $run = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($run);
    return $data['count'] > 0;
}

function getLikes($post_id){
    global $db;
    $query = "SELECT * FROM likes WHERE post_id = $post_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

//add Comment
function addComment($post_id, $comment){
    global $db;
    $comment_text = mysqli_real_escape_string($db, $comment);
    $user_id = $_SESSION['userdata']['id'];

    $query = "INSERT INTO comments(post_id, user_id, comment) VALUES($post_id, $user_id, '$comment_text')";
    return mysqli_query($db, $query);
}

function getComments($post_id){
    global $db;
    $query = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY id ASC"; // ASC để bình luận cũ hiện trên, mới hiện dưới
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

function getFollowers($user_id){
    global $db;
    // Lấy tất cả dòng mà user_id là mình (tức là người ta đang follow mình)
    $query = "SELECT * FROM follow_list WHERE user_id = $user_id"; 
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

// Hàm lấy danh sách những người mình đang follow (để hiện nút Follow/Unfollow cho đúng)
function getFollowing($user_id){
    global $db;
    $query = "SELECT * FROM follow_list WHERE follower_id = $user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

// Hàm kiểm tra xem mình có đang follow người này không (để hiện nút Follow hay Unfollow)
function checkFollowStatus($user_id_to_check){
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query = "SELECT count(*) as count FROM follow_list WHERE follower_id = $current_user AND user_id = $user_id_to_check";
    $run = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($run);
    return $data['count'] > 0; // Trả về true nếu đã follow
}

// includes/user.php

// Hàm gợi ý người dùng để Follow (Lọc những người mình chưa follow)
function filterFollowSuggestions(){
    global $db;
    $current_user = $_SESSION['userdata']['id'];

    $query = "SELECT * FROM users 
              WHERE id != $current_user 
              AND id NOT IN (SELECT user_id FROM follow_list WHERE follower_id = $current_user)
              ORDER BY RAND() LIMIT 5";
              
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC);
}

function searchUser($keyword){
    global $db;

    $query = "SELECT * FROM users 
          WHERE username LIKE '%$keyword%' 
          OR CONCAT(first_name, ' ', last_name) LIKE '%$keyword%' 
          LIMIT 5";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, MYSQLI_ASSOC); 
}
?>