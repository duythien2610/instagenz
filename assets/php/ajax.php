<?php
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/user.php';
session_start();

if (!isset($_SESSION['userdata'])) {
    echo json_encode(['status' => false, 'msg' => 'Chưa đăng nhập']);
    exit;
}

$current_user_id = $_SESSION['userdata']['id'];


// === 1. GỬI TIN NHẮN ===aaa
if (isset($_GET['sendmessage'])) {
    $to_user_id = $_POST['to_user_id'];
    $msg = mysqli_real_escape_string($db, $_POST['msg']);

    if (!empty($msg)) {
        $query = "INSERT INTO messages (from_user_id, to_user_id, msg, read_status, created_at) 
                  VALUES ($current_user_id, $to_user_id, '$msg', 0, NOW())";
        if (mysqli_query($db, $query)) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false]);
        }
    }
    exit;
}

// === 2. LẤY TIN NHẮN & CẬP NHẬT TRẠNG THÁI ĐÃ XEM ===
if (isset($_GET['getmessages'])) {
    $chat_user_id = $_POST['to_user_id'];

    // A. Khi mình mở tin nhắn của họ -> Đánh dấu tất cả tin họ gửi cho mình là ĐÃ ĐỌC (1)
    $update_query = "UPDATE messages SET read_status = 1 
                     WHERE from_user_id = $chat_user_id AND to_user_id = $current_user_id";
    mysqli_query($db, $update_query);

    // B. Lấy tin nhắn ra
    $query = "SELECT * FROM messages 
              WHERE (from_user_id = $current_user_id AND to_user_id = $chat_user_id) 
              OR (from_user_id = $chat_user_id AND to_user_id = $current_user_id) 
              ORDER BY id ASC";
    
    $run = mysqli_query($db, $query);
    $output = "";

    if (mysqli_num_rows($run) > 0) {
        foreach ($run as $row) {
            if ($row['from_user_id'] == $current_user_id) {
                // --- Tin nhắn CỦA MÌNH (Outgoing) ---
                $status_html = ($row['read_status'] == 1) 
                    ? '<br><small class="text-white-50" style="font-size:10px;">Đã xem</small>' 
                    : '<br><small class="text-white-50" style="font-size:10px;">Đã gửi</small>';

                $output .= '<div class="bubble outgoing">' . $row['msg'] . $status_html . '</div>';
            } else {
                // --- Tin nhắn NGƯỜI KHÁC gửi đến (Incoming) ---
                $output .= '<div class="bubble incoming">' . $row['msg'] . '</div>';
            }
        }
    } else {
        $output .= '<div class="text-center text-muted mt-5">Hãy bắt đầu cuộc trò chuyện!</div>';
    }
    
    echo $output;
    exit;
}

// === 3. ĐẾM TỔNG SỐ TIN NHẮN CHƯA ĐỌC (Dùng cho Navbar - Code cũ của bạn) ===
if (isset($_GET['check_unread'])) {
    $query = "SELECT COUNT(*) as unread FROM messages 
              WHERE to_user_id = $current_user_id AND read_status = 0";
    $result = mysqli_query($db, $query);
    $data = mysqli_fetch_assoc($result);
    
    echo json_encode(['status' => true, 'count' => $data['unread']]);
    exit;
}

// === 3b. ĐẾM THÔNG BÁO CHUNG (LIKE, FOLLOW, COMMENT) CHO CHUÔNG NAVBAR ===
if (isset($_GET['check_general_notifications'])) {
    // Đếm số thông báo chưa đọc của user hiện tại
    $sql = "SELECT COUNT(*) AS cnt FROM notifications 
            WHERE to_user_id = $current_user_id AND read_status = 0";
    $run = mysqli_query($db, $sql);

    if ($run) {
        $row = mysqli_fetch_assoc($run);
        echo json_encode(['status' => true, 'count' => (int)$row['cnt']]);
    } else {
        echo json_encode(['status' => false, 'count' => 0]);
    }
    exit;
}

// === 3c. LẤY DANH SÁCH THÔNG BÁO (REAL-TIME) ===
if (isset($_GET['get_notifications_list'])) {
    $notifications = getNotifications($current_user_id);
    $notifications_html = '';
    
    if (count($notifications) > 0) {
        foreach ($notifications as $notif) {
            $is_unread = ($notif['read_status'] == 0);
            $notification_class = $is_unread ? 'unread-notification' : 'read-notification';
            $name_class = $is_unread ? 'fw-bold' : 'fw-normal';
            $message_class = $is_unread ? 'fw-semibold' : '';
            $unread_dot = $is_unread ? '<span class="unread-dot me-2"></span>' : '';
            
            $notif_id = $notif['id'];
            $post_id = $notif['post_id'];
            $username = htmlspecialchars($notif['username']);
            $first_name = htmlspecialchars($notif['first_name']);
            $last_name = htmlspecialchars($notif['last_name']);
            $message = htmlspecialchars($notif['message']);
            $profile_pic = htmlspecialchars($notif['profile_pic']);
            $created_at = show_time_ago($notif['created_at']);
            $read_status = $notif['read_status'];
            
            $notifications_html .= '
                <li>
                    <a class="notification-item dropdown-item d-flex align-items-center p-2 ' . $notification_class . '" 
                       href="javascript:void(0);" 
                       data-notif-id="' . $notif_id . '"
                       data-post-id="' . $post_id . '"
                       data-username="' . $username . '"
                       data-read-status="' . $read_status . '">
                        ' . $unread_dot . '
                        <img src="assets/images/profile/' . $profile_pic . '" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                        <div class="ms-2 text-wrap flex-grow-1">
                            <span class="' . $name_class . '">' . $first_name . ' ' . $last_name . '</span>
                            <span class="' . $message_class . '">' . $message . '</span>
                            <br>
                            <small class="text-muted">' . $created_at . '</small>
                        </div>
                    </a>
                </li>';
        }
    } else {
        $notifications_html = '<li class="p-3 text-center text-muted">Không có thông báo nào.</li>';
    }
    
    echo json_encode(['status' => true, 'html' => $notifications_html]);
    exit;
}

// === 4. [MỚI] CHECK THÔNG BÁO TỪNG NGƯỜI (Dùng cho Sidebar chấm đỏ) ===
if (isset($_GET['check_notifications'])) {
    // Logic: Nhóm theo người gửi (from_user_id) và đếm số lượng tin chưa đọc
    $query = "SELECT from_user_id, COUNT(*) as unread_count 
              FROM messages 
              WHERE to_user_id = $current_user_id AND read_status = 0 
              GROUP BY from_user_id";
              
    $run = mysqli_query($db, $query);
    $data = [];

    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = [
                'from_user_id' => $row['from_user_id'],
                'unread_count' => $row['unread_count']
            ];
        }
    }
    
    // Trả về JSON: ví dụ [{"from_user_id":1, "unread_count":5}, {"from_user_id":3, "unread_count":1}]
    echo json_encode($data);
    exit;
}


 //=== 5. [MỚI] XÓA CUỘC TRÒ CHUYỆN ===
if (isset($_GET['delete_chat'])) {
    $partner_id = $_POST['to_user_id'];
    
    // Xóa tất cả tin nhắn giữa User hiện tại và Partner (2 chiều)
    $query = "DELETE FROM messages 
              WHERE (from_user_id = $current_user_id AND to_user_id = $partner_id) 
              OR (from_user_id = $partner_id AND to_user_id = $current_user_id)";
              
    if (mysqli_query($db, $query)) {
        echo json_encode(['status' => true]);
    } else {
        echo json_encode(['status' => false]);
    }
    exit;
}


//=================================================================
// UNFOLLO, FOLLOW, SEARCH 
// --- XỬ LÝ FOLLOW ---
if(isset($_GET['follow'])){
    $user_id = $_POST['user_id']; // Người được follow
    $follower_id = $_SESSION['userdata']['id']; // Tôi (người đang bấm nút)

    // Kiểm tra xem đã follow chưa để tránh trùng lặp
    $check = mysqli_query($db, "SELECT * FROM follow_list WHERE follower_id=$follower_id AND user_id=$user_id");
    
    if(mysqli_num_rows($check) == 0){
        // Thêm vào bảng follow_list
        $query = "INSERT INTO follow_list(follower_id, user_id) VALUES($follower_id, $user_id)";
        
        if(mysqli_query($db, $query)){
            // Gửi thông báo cho người được follow
            sendNotification($follower_id, $user_id, 0, 'đã bắt đầu theo dõi bạn.');
            
            echo json_encode(['status'=>true]);
        } else {
            echo json_encode(['status'=>false, 'msg' => 'Lỗi DB: '.mysqli_error($db)]);
        }
    } else {
        // Đã follow rồi thì cứ báo thành công
        echo json_encode(['status'=>true]);
    }
    exit; // Dừng script ngay
}

// --- XỬ LÝ UNFOLLOW ---
if(isset($_GET['unfollow'])){
    $user_id = $_POST['user_id'];
    $follower_id = $_SESSION['userdata']['id'];

    $query = "DELETE FROM follow_list WHERE follower_id=$follower_id AND user_id=$user_id";
    
    if(mysqli_query($db, $query)){
        echo json_encode(['status'=>true]);
    } else {
        echo json_encode(['status'=>false, 'msg' => 'Lỗi DB: '.mysqli_error($db)]);
    }
    exit;
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

// =============================================================
// LIKE, COMMENT, FOLLOW (
// =============================================================

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

// === TÌM KIẾM NGƯỜI FOLLOW CHO @MENTION ===
if(isset($_GET['search_following'])){
    $search_term = mysqli_real_escape_string($db, $_POST['search'] ?? '');
    $current_user_id = $_SESSION['userdata']['id'];
    
    // Tìm những người đang follow có username hoặc tên khớp với search_term
    $query = "SELECT u.id, u.username, u.first_name, u.last_name, u.profile_pic 
              FROM users u
              INNER JOIN follow_list fl ON u.id = fl.user_id
              WHERE fl.follower_id = $current_user_id
              AND (u.username LIKE '%$search_term%' OR u.first_name LIKE '%$search_term%' OR u.last_name LIKE '%$search_term%')
              LIMIT 10";
    
    $run = mysqli_query($db, $query);
    $results = array();
    
    if ($run && mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_assoc($run)) {
            $results[] = array(
                'id' => $row['id'],
                'username' => $row['username'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'profile_pic' => $row['profile_pic']
            );
        }
    }
    
    echo json_encode(['status' => true, 'users' => $results]);
    exit;
}

if(isset($_GET['addcomment'])){
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
    $mentioned_user_id = isset($_POST['mentioned_user_id']) ? (int)$_POST['mentioned_user_id'] : 0;

    // Xử lý @mention trong comment - tìm user_id từ username
    $mention_pattern = '/@(\w+)/';
    if (preg_match_all($mention_pattern, $comment, $matches)) {
        foreach ($matches[1] as $username) {
            $username_escaped = mysqli_real_escape_string($db, $username);
            $user_query = mysqli_query($db, "SELECT id FROM users WHERE username = '$username_escaped'");
            if ($user_query && $user_row = mysqli_fetch_assoc($user_query)) {
                if (!$mentioned_user_id) {
                    $mentioned_user_id = $user_row['id'];
                }
            }
        }
    }

    if(addComment($post_id, $comment, $parent_id, $mentioned_user_id)){
        $cuser = getUser($_SESSION['userdata']['id']);
        $comment_id = mysqli_insert_id($db);

        // Gửi thông báo cho chủ bài viết (chỉ khi comment gốc, không phải reply)
        if ($parent_id == 0) {
            $post_query = mysqli_query($db, "SELECT user_id FROM posts WHERE id=$post_id");
            if($post_query){
                $post_data = mysqli_fetch_assoc($post_query);
                if($post_data){
                    $owner_id = $post_data['user_id'];
                    // Chỉ gửi thông báo nếu không phải chính mình
                    if ($owner_id != $cuser['id']) {
                        sendNotification($cuser['id'], $owner_id, $post_id, 'đã bình luận bài viết của bạn.');
                    }
                }
            }
        } else {
            // Nếu là reply, gửi thông báo cho người được reply
            $parent_comment_query = mysqli_query($db, "SELECT user_id FROM comments WHERE id = $parent_id");
            if ($parent_comment_query) {
                $parent_data = mysqli_fetch_assoc($parent_comment_query);
                if ($parent_data && $parent_data['user_id'] != $cuser['id']) {
                    sendNotification($cuser['id'], $parent_data['user_id'], $post_id, 'đã trả lời bình luận của bạn.');
                }
            }
        }

        // Format comment với @mention
        $formatted_comment = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', htmlspecialchars($comment));
        
        // Kiểm tra xem đây là comment gốc hay reply
        if ($parent_id > 0) {
            // Đây là reply - kiểm tra xem có phải reply của reply không
            $check_column = mysqli_query($db, "SHOW COLUMNS FROM comments LIKE 'parent_id'");
            $has_parent_id = ($check_column && mysqli_num_rows($check_column) > 0);
            
            $is_nested_reply = false;
            if ($has_parent_id) {
                $parent_comment_query = mysqli_query($db, "SELECT parent_id FROM comments WHERE id = $parent_id");
                if ($parent_comment_query) {
                    $parent_comment_data = mysqli_fetch_assoc($parent_comment_query);
                    $is_nested_reply = ($parent_comment_data && $parent_comment_data['parent_id'] > 0);
                }
            }
            
            if ($is_nested_reply) {
                // Reply của reply - nested reply (level 2)
                $comment_html = '<div class="comment-nested-reply" data-comment-id="' . $comment_id . '">
                    <div class="d-flex align-items-start mb-2">
                        <img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                        <div class="ms-2 flex-grow-1">
                            <div class="bg-light p-2 rounded" style="font-size: 12px; border-left: 2px solid #dee2e6;">
                                <span class="fw-bold">'.$cuser['first_name'].' '.$cuser['last_name'].'</span>
                                <span>' . $formatted_comment . '</span>
                            </div>
                            <div class="mt-1">
                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $comment_id . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                            </div>
                        </div>
                    </div>
                    <div class="reply-container-popup-' . $comment_id . '"></div>
                </div>';
            } else {
                // Reply của comment gốc (level 1)
                $comment_html = '<div class="comment-reply-wrapper" data-comment-id="' . $comment_id . '">
                    <div class="d-flex align-items-start mb-2">
                        <img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" style="width:32px; height:32px; object-fit:cover">
                        <div class="ms-2 flex-grow-1">
                            <div class="bg-light p-2 rounded" style="font-size: 13px; border-left: 2px solid #0d6efd;">
                                <span class="fw-bold">'.$cuser['first_name'].' '.$cuser['last_name'].'</span>
                                <span>' . $formatted_comment . '</span>
                            </div>
                            <div class="mt-1">
                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $comment_id . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                            </div>
                            <div class="reply-container-popup-' . $comment_id . ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            // Comment gốc (level 0)
            $comment_html = '<div class="comment-item mb-3" data-comment-id="' . $comment_id . '">
                <div class="d-flex align-items-start">
                    <img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover; flex-shrink:0;">
                    <div class="ms-2 flex-grow-1">
                        <div class="bg-light p-2 rounded mb-1">
                            <span class="fw-bold" style="font-size:14px">'.$cuser['first_name'].' '.$cuser['last_name'].'</span>
                            <span style="font-size:14px">' . $formatted_comment . '</span>
                        </div>
                        <div class="mb-2">
                            <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $comment_id . '" data-post-id="' . $post_id . '" style="font-size: 12px;">Trả lời</button>
                        </div>
                        <div class="reply-container-popup-' . $comment_id . ' ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                    </div>
                </div>
            </div>';
        }
        echo json_encode(['status'=>true, 'comment'=>$comment_html, 'comment_id' => $comment_id, 'parent_id' => $parent_id]);
    }else{
        echo json_encode(['status'=>false]);
    }
}

// Lấy replies của một comment
if(isset($_GET['get_replies'])){
    $comment_id = (int)$_POST['comment_id'];
    $post_id = (int)$_POST['post_id'];
    
    $replies = getComments($post_id, $comment_id);
    $replies_html = '';
    
    foreach($replies as $reply){
        $ruser = getUser($reply['user_id']);
        $formatted_reply = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', htmlspecialchars($reply['comment']));
        
        $replies_html .= '<div class="comment-item comment-reply ms-5" data-comment-id="' . $reply['id'] . '">
            <div class="d-flex align-items-start p-2">
                <img src="assets/images/profile/'.$ruser['profile_pic'].'" class="rounded-circle border" style="width:30px; height:30px; object-fit:cover">
                <div class="ms-2 flex-grow-1">
                    <div class="bg-light p-2 rounded" style="font-size: 13px;">
                        <span class="fw-bold">'.$ruser['first_name'].' '.$ruser['last_name'].'</span>
                        <span class="comment-text">' . $formatted_reply . '</span>
                    </div>
                    <div class="mt-1">
                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="' . $reply['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                    </div>
                </div>
            </div>
            <div class="reply-container-' . $reply['id'] . '"></div>
        </div>';
    }
    
    echo json_encode(['status' => true, 'html' => $replies_html]);
    exit;
}

// === REALTIME UPDATES - Lấy tất cả thay đổi mới ===
if(isset($_GET['get_realtime_updates'])){
    require_once '../../includes/user.php';
    
    $current_user_id = $_SESSION['userdata']['id'];
    $post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : array();
    $last_comment_ids = isset($_POST['last_comment_ids']) ? $_POST['last_comment_ids'] : array(); // ID của comment cuối cùng cho mỗi post
    $last_update_time = isset($_POST['last_update_time']) ? (int)$_POST['last_update_time'] : 0;
    
    $updates = array(
        'posts' => array(), // Posts mới
        'post_updates' => array(), // Cập nhật cho các post hiện có
        'timestamp' => time()
    );
    
    // Lấy posts mới (sau thời điểm last_update_time) - chỉ lấy posts của người đang follow hoặc chính mình
    if ($last_update_time > 0) {
        $new_posts_query = "SELECT p.*, u.first_name, u.last_name, u.username, u.profile_pic 
                           FROM posts p 
                           JOIN users u ON p.user_id = u.id 
                           WHERE (p.user_id IN (
                               SELECT user_id FROM follow_list WHERE follower_id = $current_user_id
                           ) OR p.user_id = $current_user_id)
                           AND UNIX_TIMESTAMP(p.created_at) > $last_update_time
                           ORDER BY p.id DESC 
                           LIMIT 10";
        $new_posts_run = mysqli_query($db, $new_posts_query);
        if ($new_posts_run) {
            while ($new_post = mysqli_fetch_assoc($new_posts_run)) {
                $updates['posts'][] = $new_post;
            }
        }
    }
    
    // Cập nhật cho các post hiện có
    if (!empty($post_ids) && is_array($post_ids)) {
        foreach ($post_ids as $post_id) {
            $post_id = (int)$post_id;
            $last_comment_id = isset($last_comment_ids[$post_id]) ? (int)$last_comment_ids[$post_id] : 0;
            
            // Lấy số lượng likes hiện tại
            $likes_query = "SELECT COUNT(*) AS c FROM likes WHERE post_id = $post_id";
            $likes_run = mysqli_query($db, $likes_query);
            $likes_row = mysqli_fetch_assoc($likes_run);
            $likes_count = (int)$likes_row['c'];
            $is_liked = checkLikeStatus($post_id);
            
            // Lấy số lượng comments hiện tại
            $comments_query = "SELECT COUNT(*) AS c FROM comments WHERE post_id = $post_id";
            $comments_run = mysqli_query($db, $comments_query);
            $comments_row = mysqli_fetch_assoc($comments_run);
            $comments_count = (int)$comments_row['c'];
            
            // Lấy comments mới (sau comment_id cuối cùng)
            $new_comments = array();
            $max_comment_id = $last_comment_id;
            
            if ($last_comment_id > 0) {
                $new_comments_query = "SELECT * FROM comments WHERE post_id = $post_id AND id > $last_comment_id ORDER BY id ASC";
            } else {
                // Lấy comment cuối cùng để làm baseline
                $last_comment_query = mysqli_query($db, "SELECT MAX(id) as max_id FROM comments WHERE post_id = $post_id");
                if ($last_comment_query) {
                    $last_comment_data = mysqli_fetch_assoc($last_comment_query);
                    $max_comment_id = $last_comment_data['max_id'] ? (int)$last_comment_data['max_id'] : 0;
                }
                // Không trả về tất cả comments để tránh quá tải, chỉ trả về ID của comment cuối cùng
                $new_comments_query = "SELECT * FROM comments WHERE post_id = $post_id AND id = 0"; // Không lấy gì
            }
            
            $new_comments_run = mysqli_query($db, $new_comments_query);
            if ($new_comments_run && mysqli_num_rows($new_comments_run) > 0) {
                while ($new_comment = mysqli_fetch_assoc($new_comments_run)) {
                    $comment_user = getUser($new_comment['user_id']);
                    if (!$comment_user) continue;
                    
                    $comment_text = htmlspecialchars($new_comment['comment']);
                    $comment_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $comment_text);
                    
                    // Xác định loại comment (gốc, reply, nested reply)
                    $comment_type = 'main';
                    $parent_id = isset($new_comment['parent_id']) ? (int)$new_comment['parent_id'] : 0;
                    if ($parent_id > 0) {
                        $check_column = mysqli_query($db, "SHOW COLUMNS FROM comments LIKE 'parent_id'");
                        $has_parent_id = ($check_column && mysqli_num_rows($check_column) > 0);
                        if ($has_parent_id) {
                            $parent_comment_query = mysqli_query($db, "SELECT parent_id FROM comments WHERE id = $parent_id");
                            if ($parent_comment_query) {
                                $parent_data = mysqli_fetch_assoc($parent_comment_query);
                                $comment_type = ($parent_data && $parent_data['parent_id'] > 0) ? 'nested' : 'reply';
                            } else {
                                $comment_type = 'reply';
                            }
                        } else {
                            $comment_type = 'reply';
                        }
                    }
                    
                    // Tạo HTML cho comment - format cho wall
                    if ($comment_type == 'nested') {
                        $comment_html = '<div class="comment-nested-reply" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start mb-2">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded" style="font-size: 12px; border-left: 2px solid #dee2e6;">
                                        <span class="fw-bold">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span>' . $comment_text . '</span>
                                    </div>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                                    </div>
                                </div>
                            </div>
                            <div class="reply-container-' . $new_comment['id'] . '"></div>
                        </div>';
                    } elseif ($comment_type == 'reply') {
                        $comment_html = '<div class="comment-reply-wrapper" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start mb-2">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:32px; height:32px; object-fit:cover">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded" style="font-size: 13px; border-left: 2px solid #0d6efd;">
                                        <span class="fw-bold">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span>' . $comment_text . '</span>
                                    </div>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                                    </div>
                                    <div class="reply-container-' . $new_comment['id'] . ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                                </div>
                            </div>
                        </div>';
                    } else {
                        // Comment gốc - format cho wall
                        $comment_html = '<div class="comment-item mb-3" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover; flex-shrink:0;">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded mb-1">
                                        <span class="fw-bold" style="font-size:14px">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span style="font-size:14px">' . $comment_text . '</span>
                                    </div>
                                    <div class="mb-2">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 12px;">Trả lời</button>
                                    </div>
                                    <div class="reply-container-' . $new_comment['id'] . ' ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                                </div>
                            </div>
                        </div>';
                    }
                    
                    // Tạo HTML cho modal (nếu cần)
                    if ($comment_type == 'nested') {
                        $comment_html_popup = '<div class="comment-nested-reply" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start mb-2">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded" style="font-size: 12px; border-left: 2px solid #dee2e6;">
                                        <span class="fw-bold">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span>' . $comment_text . '</span>
                                    </div>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                                    </div>
                                </div>
                            </div>
                            <div class="reply-container-popup-' . $new_comment['id'] . '"></div>
                        </div>';
                    } elseif ($comment_type == 'reply') {
                        $comment_html_popup = '<div class="comment-reply-wrapper" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start mb-2">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:32px; height:32px; object-fit:cover">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded" style="font-size: 13px; border-left: 2px solid #0d6efd;">
                                        <span class="fw-bold">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span>' . $comment_text . '</span>
                                    </div>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                                    </div>
                                    <div class="reply-container-popup-' . $new_comment['id'] . ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                                </div>
                            </div>
                        </div>';
                    } else {
                        $comment_html_popup = '<div class="comment-item mb-3" data-comment-id="' . $new_comment['id'] . '">
                            <div class="d-flex align-items-start">
                                <img src="assets/images/profile/'.$comment_user['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover; flex-shrink:0;">
                                <div class="ms-2 flex-grow-1">
                                    <div class="bg-light p-2 rounded mb-1">
                                        <span class="fw-bold" style="font-size:14px">'.$comment_user['first_name'].' '.$comment_user['last_name'].'</span>
                                        <span style="font-size:14px">' . $comment_text . '</span>
                                    </div>
                                    <div class="mb-2">
                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $new_comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 12px;">Trả lời</button>
                                    </div>
                                    <div class="reply-container-popup-' . $new_comment['id'] . ' ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>
                                </div>
                            </div>
                        </div>';
                    }
                    
                    $new_comments[] = array(
                        'id' => $new_comment['id'],
                        'html' => $comment_html, // HTML cho wall
                        'html_popup' => $comment_html_popup, // HTML cho modal
                        'parent_id' => $parent_id,
                        'type' => $comment_type
                    );
                    
                    if ($new_comment['id'] > $max_comment_id) {
                        $max_comment_id = $new_comment['id'];
                    }
                }
            }
            
            $updates['post_updates'][$post_id] = array(
                'likes_count' => $likes_count,
                'is_liked' => $is_liked,
                'comments_count' => $comments_count,
                'new_comments' => $new_comments,
                'last_comment_id' => $max_comment_id
            );
        }
    }
    
    echo json_encode(array('status' => true, 'updates' => $updates));
    exit;
}

// === ENDPOINT CŨ - Cập nhật số lượng likes/comments ===
if(isset($_GET['get_post_updates'])){
    $post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : array();
    $current_user_id = $_SESSION['userdata']['id'];
    $updates = array();
    
    if (!empty($post_ids) && is_array($post_ids)) {
        foreach ($post_ids as $post_id) {
            $post_id = (int)$post_id;
            $likes_query = "SELECT COUNT(*) AS c FROM likes WHERE post_id = $post_id";
            $likes_run = mysqli_query($db, $likes_query);
            $likes_row = mysqli_fetch_assoc($likes_run);
            $likes_count = (int)$likes_row['c'];
            
            $comments_query = "SELECT COUNT(*) AS c FROM comments WHERE post_id = $post_id";
            $comments_run = mysqli_query($db, $comments_query);
            $comments_row = mysqli_fetch_assoc($comments_run);
            $comments_count = (int)$comments_row['c'];
            
            $is_liked = checkLikeStatus($post_id);
            
            $updates[$post_id] = array(
                'likes' => $likes_count,
                'comments' => $comments_count,
                'is_liked' => $is_liked
            );
        }
    }
    
    echo json_encode(['status' => true, 'data' => $updates]);
    exit;
}

// === ENDPOINT CŨ - Cập nhật số lượng likes/comments ===
if(isset($_GET['get_post_updates'])){
    $post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : array();
    $current_user_id = $_SESSION['userdata']['id'];
    $updates = array();
    
    if (!empty($post_ids) && is_array($post_ids)) {
        foreach ($post_ids as $post_id) {
            $post_id = (int)$post_id;
            $likes_count = admin_get_single_count("SELECT COUNT(*) AS c FROM likes WHERE post_id = $post_id");
            $comments_count = admin_get_single_count("SELECT COUNT(*) AS c FROM comments WHERE post_id = $post_id");
            $is_liked = checkLikeStatus($post_id);
            
            $updates[$post_id] = array(
                'likes' => $likes_count,
                'comments' => $comments_count,
                'is_liked' => $is_liked
            );
        }
    }
    
    echo json_encode(['status' => true, 'data' => $updates]);
    exit;
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

                $btn = '<button class="btn btn-sm btn-danger unfollowbtn" data-user-id="'.$user['id'].'">Bỏ theo dõi</button>';
            } else {

                $btn = '<button class="btn btn-sm btn-primary followbtn" data-user-id="'.$user['id'].'">Theo dõi</button>';
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

    // Chỉ lấy comments gốc (không có parent_id)
    $parent_comments = array_filter($comments, function($c) {
        return !isset($c['parent_id']) || $c['parent_id'] == 0;
    });

    foreach($parent_comments as $comment){
        $cuser = getUser($comment['user_id']);
        $comment_text = htmlspecialchars($comment['comment']);
        // Xử lý @mention trong comment
        $comment_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $comment_text);
        
        // Lấy replies của comment này
        $replies = getComments($post_id, $comment['id']);
        $replies_html = '';
        
        foreach($replies as $reply){
            $ruser = getUser($reply['user_id']);
            $reply_text = htmlspecialchars($reply['comment']);
            $reply_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $reply_text);
            
            // Lấy nested replies của reply này (replies của replies)
            $nested_replies = getComments($post_id, $reply['id']);
            $nested_replies_html = '';
            
            foreach($nested_replies as $nested_reply){
                $nruser = getUser($nested_reply['user_id']);
                $nested_reply_text = htmlspecialchars($nested_reply['comment']);
                $nested_reply_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $nested_reply_text);
                
                $nested_replies_html .= '<div class="comment-nested-reply" data-comment-id="' . $nested_reply['id'] . '">
                    <div class="d-flex align-items-start mb-2">
                        <img src="assets/images/profile/'.$nruser['profile_pic'].'" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                        <div class="ms-2 flex-grow-1">
                            <div class="bg-light p-2 rounded" style="font-size: 12px; border-left: 2px solid #dee2e6;">
                                <span class="fw-bold">'.$nruser['first_name'].' '.$nruser['last_name'].'</span>
                                <span>'.$nested_reply_text.'</span>
                            </div>
                            <div class="mt-1">
                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $nested_reply['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                            </div>
                        </div>
                    </div>
                    <div class="reply-container-popup-' . $nested_reply['id'] . '"></div>
                </div>';
            }
            
            $replies_html .= '<div class="comment-reply-wrapper" data-comment-id="' . $reply['id'] . '">
                <div class="d-flex align-items-start mb-2">
                    <img src="assets/images/profile/'.$ruser['profile_pic'].'" class="rounded-circle border" style="width:32px; height:32px; object-fit:cover">
                    <div class="ms-2 flex-grow-1">
                        <div class="bg-light p-2 rounded" style="font-size: 13px; border-left: 2px solid #0d6efd;">
                            <span class="fw-bold">'.$ruser['first_name'].' '.$ruser['last_name'].'</span>
                            <span>'.$reply_text.'</span>
                        </div>
                        <div class="mt-1">
                            <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $reply['id'] . '" data-post-id="' . $post_id . '" style="font-size: 11px;">Trả lời</button>
                        </div>
                        <div class="reply-container-popup-' . $reply['id'] . ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;">' . $nested_replies_html . '</div>
                    </div>
                </div>
            </div>';
        }
        
        $comments_html .= '<div class="comment-item mb-3" data-comment-id="' . $comment['id'] . '">
            <div class="d-flex align-items-start">
                <img src="assets/images/profile/'.$cuser['profile_pic'].'" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover; flex-shrink:0;">
                <div class="ms-2 flex-grow-1">
                    <div class="bg-light p-2 rounded mb-1">
                        <span class="fw-bold" style="font-size:14px">'.$cuser['first_name'].' '.$cuser['last_name'].'</span>
                        <span style="font-size:14px">'.$comment_text.'</span>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn-popup" data-comment-id="' . $comment['id'] . '" data-post-id="' . $post_id . '" style="font-size: 12px;">Trả lời</button>
                    </div>
                    <div class="reply-container-popup-' . $comment['id'] . ' ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;">' . $replies_html . '</div>
                </div>
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
    // Đường dẫn tuyệt đối tới thư mục ảnh post
    $post_img_dir = __DIR__ . '/../images/posts/';
    if($row['post_img'] != '' && file_exists($post_img_dir.$row['post_img'])){
        unlink($post_img_dir.$row['post_img']);
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
    echo json_encode(['status' => true]);
    exit;
}

// Đánh dấu một thông báo cụ thể đã đọc
if(isset($_GET['mark_notification_read'])){
    $notification_id = (int)$_POST['notification_id'];
    $user_id = $_SESSION['userdata']['id'];
    
    // Đảm bảo chỉ đánh dấu thông báo của user hiện tại
    $query = "UPDATE notifications SET read_status = 1 WHERE id = $notification_id AND to_user_id = $user_id";
    if(mysqli_query($db, $query)){
        echo json_encode(['status' => true]);
    } else {
        echo json_encode(['status' => false]);
    }
    exit;
}

