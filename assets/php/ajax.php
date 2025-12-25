<?php
require_once '../../includes/db.php';
require_once '../../includes/user.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['userdata'])){
    echo json_encode(['status'=>false, 'msg'=>'Chưa đăng nhập']);
    die();
}

if(isset($_GET['like'])){
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['userdata']['id'];
    if(!checkLikeStatus($post_id)){
        $query = "INSERT INTO likes(post_id, user_id) VALUES($post_id, $user_id)";
        
        if(mysqli_query($db, $query)){
            $post_query = mysqli_query($db, "SELECT user_id FROM posts WHERE id=$post_id");
            $post_data = mysqli_fetch_assoc($post_query);
            $owner_id = $post_data['user_id'];

            sendNotification($user_id, $owner_id, $post_id, 'đã thích bài viết của bạn.');

            echo json_encode(['status'=>true, 'action'=>'liked']);
        } else {
            echo json_encode(['status'=>false, 'msg'=>'Lỗi không thể like']);
        }

    } else {
        $query = "DELETE FROM likes WHERE post_id=$post_id AND user_id=$user_id";
        
        if(mysqli_query($db, $query)){
            echo json_encode(['status'=>true, 'action'=>'unliked']);
        } else {
            echo json_encode(['status'=>false, 'msg'=>'Lỗi không thể unlike']);
        }
    }
}

if(isset($_GET['unlike'])){
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['userdata']['id'];

    if(checkLikeStatus($post_id)){

        $query = "DELETE FROM likes WHERE post_id = $post_id AND user_id=$user_id";
        if(mysqli_query($db, $query)){
            echo json_encode(['status'=>true]);
        }else{
            echo json_encode(['status'=>false]);
        }
    } else {

        echo json_encode(['status'=>true]);
    }
}

if(isset($_GET['addcomment'])){
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    if(addComment($post_id, $comment)){

        $cuser = getUser($_SESSION['userdata']['id']);

        $comment_html ='<div class="d-flex align-items-center p-2">
            <div><img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" height="40"></div>
            <div class="ms-2">
                <h6 class="m-0 fw-bold">'.$cuser['first_name'].' '.$cuser['last_name'].'</h6>
                <p class="m-0 text-muted">'.$comment.'</p>
            </div>
        </div>';
        echo json_encode(['status'=>true, 'comment'=>$comment_html]);
    }else{
        echo json_encode(['status'=>false]);
    }
}

if(isset($_GET['get_likes'])){
    $post_id = $_POST['post_id'];
    
    $likes = getLikes($post_id);
    
    $html = '';
    
    foreach($likes as $like){
        $user = getUser($like['user_id']);
        if(!$user) continue;

        $is_followed = checkFollowStatus($user['id']);
        $is_me = ($user['id'] == $_SESSION['userdata']['id']);


        $btn = '';
        if(!$is_me){ 
            if($is_followed){

                $btn = '<button class="btn btn-sm btn-danger unfollowbtn" data-user-id="'.$user['id'].'">Unfollow</button>';
            } else {

                $btn = '<button class="btn btn-sm btn-primary followbtn" data-user-id="'.$user['id'].'">Follow</button>';
            }
        }
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <a href="?u='.$user['username'].'" class="text-decoration-none text-dark">
                            <img src="assets/images/profile/'.$user['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                        </a>
                        <div class="ms-2">
                            <a href="?u='.$user['username'].'" class="text-decoration-none text-dark">
                                <h6 class="m-0 fw-bold" style="font-size: 14px;">'.$user['first_name'].' '.$user['last_name'].'</h6>
                            </a>
                            <small class="text-muted">@'.$user['username'].'</small>
                        </div>
                    </div>
                    '.$btn.'
                  </div>';
    }
    
    if(count($likes) < 1){
        $html = '<p class="text-center text-muted">Chưa có ai thả tim bài viết này.</p>';
    }

    echo json_encode(['status'=>true, 'html'=>$html]);
}

if(isset($_GET['get_post_view'])){
    $post_id = $_POST['post_id'];

    $query = "SELECT * FROM posts WHERE id = $post_id";
    $run = mysqli_query($db, $query);
    $post = mysqli_fetch_assoc($run);

    $user = getUser($post['user_id']);
    $comments = getComments($post_id);
    $likes = getLikes($post_id);
    $current_user_id = $_SESSION['userdata']['id'];
    $is_own_post = ($post['user_id'] == $current_user_id) ? true : false;

    $comments_html = '';
    $is_liked = checkLikeStatus($post_id);

    foreach($comments as $comment){
        $cuser = getUser($comment['user_id']);
        $comments_html .= '<div class="d-flex align-items-start mb-2">
            <img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" style="width:35px; height:35px; object-fit:cover">
            <div class="ms-2 bg-light p-2 rounded">
                <div class="fw-bold" style="font-size:14px">'.$cuser['first_name'].' '.$cuser['last_name'].'</div>
                <div style="font-size:14px">'.$comment['comment'].'</div>
            </div>
        </div>';
    }

    echo json_encode([
        'status' => true,
        'post_img' => $post['post_img'], 
        'profile_pic' => $user['profile_pic'],
        'fullname' => $user['first_name'].' '.$user['last_name'],
        'username' => $user['username'],
        'post_text' => $post['post_text'],
        'created_at' => date('d M Y', strtotime($post['created_at'])),
        'comments_html' => $comments_html,
        'like_count' => count($likes),
        'id' => $post['id'], // Thêm key 'id' ngắn gọn để JS dễ gọi response.id
        'is_liked' => $is_liked,
        'is_own_post' => $is_own_post // QUAN TRỌNG: Biến này quyết định hiện menu Sửa hay Báo cáo
    ]);
}
if(isset($_POST['follow_user'])){
    $user_id = $_POST['user_id'];               // Người được follow (Họ)
    $follower_id = $_SESSION['userdata']['id']; // Người đi follow (Mình)
    if($user_id == $follower_id){
        echo json_encode(['status'=>false, 'msg'=>'Bạn không thể tự theo dõi chính mình']);
        die();
    }
    if(!checkFollowStatus($user_id)){
        
        $query = "INSERT INTO follow_list(follower_id, user_id) VALUES($follower_id, $user_id)";
        
        if(mysqli_query($db, $query)){
            

            sendNotification($follower_id, $user_id, 0, 'đã bắt đầu theo dõi bạn.');

            echo json_encode(['status'=>true, 'action'=>'followed']);
        }else{
            echo json_encode(['status'=>false, 'msg'=>'Lỗi hệ thống']);
        }

    }else{
        echo json_encode(['status'=>true, 'msg'=>'Đã theo dõi người này rồi']);
    }
}
if(isset($_POST['unfollow_user'])){
    $user_id = $_POST['user_id'];
    $follower_id = $_SESSION['userdata']['id'];
    
 
    if(checkFollowStatus($user_id)){
        $query = "DELETE FROM follow_list WHERE follower_id=$follower_id AND user_id=$user_id";
        if(mysqli_query($db, $query)){
            echo json_encode(['status'=>true]);
        }else{
            echo json_encode(['status'=>false]);
        }
    }else{
        echo json_encode(['status'=>true]);
    }
}


if(isset($_GET['search_mode'])){
    $keyword = mysqli_real_escape_string($db, $_POST['keyword']);
    

    $query = "SELECT * FROM users WHERE username LIKE '%$keyword%' OR first_name LIKE '%$keyword%' OR last_name LIKE '%$keyword%' LIMIT 5";
    $run = mysqli_query($db, $query);
    $users = mysqli_fetch_all($run, MYSQLI_ASSOC);

    $html = '';
    
    if(count($users) > 0){
        foreach($users as $user){

            $html .= '
            <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                <div class="d-flex align-items-center">
                    <a href="?u='.$user['username'].'">
                        <img src="assets/images/profile/'.$user['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                    </a>
                    <div class="ms-2 text-start">
                        <a href="?u='.$user['username'].'" class="text-decoration-none text-dark">
                            <h6 class="m-0 fw-bold" style="font-size: 14px;">'.$user['first_name'].' '.$user['last_name'].'</h6>
                        </a>
                        <small class="text-muted">@'.$user['username'].'</small>
                    </div>
                </div>
            </div>';
        }
        $html .= '<a href="index.php?search='.$keyword.'" class="d-block text-center p-2 text-decoration-none small">See all results for "'.$keyword.'"</a>';
        
        echo json_encode(['status'=>true, 'html'=>$html]);
    } else {
        echo json_encode(['status'=>false]);
    }
}

if(isset($_GET['delete_post'])){
    if(!isset($_SESSION['userdata'])){
        echo json_encode(['status'=>false,'msg'=>'Chưa đăng nhập']);
        exit;
    }

    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['userdata']['id'];
    $check = mysqli_query($db, "SELECT * FROM posts WHERE id=$post_id");

    if(mysqli_num_rows($check) == 0){
        echo json_encode(['status'=>false,'msg'=>'Không có quyền hoặc bài viết không tồn tại']);
        exit;
    }

    $row = mysqli_fetch_assoc($check);
    if($row['post_img'] != '' && file_exists('../assets/images/posts/'.$row['post_img'])){
        unlink('../assets/images/posts/'.$row['post_img']);
    }
    mysqli_query($db, "DELETE FROM likes WHERE post_id=$post_id");
    mysqli_query($db, "DELETE FROM comments WHERE post_id=$post_id");
    mysqli_query($db, "DELETE FROM posts WHERE id=$post_id");

    echo json_encode(['status'=>true]);
    exit;
}

if(isset($_GET['edit_post'])){
    $post_id = $_POST['post_id'];
    $post_text = mysqli_real_escape_string($db, $_POST['post_text']);

    $sql = "UPDATE posts SET post_text = '$post_text' WHERE id = $post_id";
    
    if(mysqli_query($db, $sql)){
        echo json_encode(['status'=>true, 'msg'=>'Cập nhật thành công']);
    }else{
        echo json_encode(['status'=>false, 'msg'=>'Lỗi hệ thống']);
    }
}

if(isset($_GET['read_notification'])){
    $user_id = $_SESSION['userdata']['id'];
    mysqli_query($db, "UPDATE notifications SET read_status = 1 WHERE to_user_id = $user_id");
}
