<?php
global $db; // Gọi biến kết nối db

// 1. Lấy user hiện tại
$user = $_SESSION['userdata'];
$user_id = $user['id'];

// 2. Lấy danh sách bạn bè
$chat_users = getFollowing($user['id']); 

// 3. Xử lý logic hiển thị
$chat_with_user = null;
$chat_with_id = null;

if (isset($_GET['uid'])) {
    $chat_with_id = $_GET['uid'];
    $chat_with_user = getUser($chat_with_id);
}
?>

<style>
    /* Scrollbar đẹp */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

    body.dark-mode ::-webkit-scrollbar-track { background: #1a1a1a; }
    body.dark-mode ::-webkit-scrollbar-thumb { background: #444; }
    body.dark-mode ::-webkit-scrollbar-thumb:hover { background: #555; }

    body { overflow-y: hidden; } 
    
    .msg-container { 
        height: calc(100vh - 90px); 
        margin-top: 15px; 
        background: #fff; 
        border: 1px solid #dbdbdb; 
        display: flex; 
        overflow: hidden; 
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .chat-sidebar { 
        width: 350px; 
        border-right: 1px solid #dbdbdb; 
        overflow-y: auto; 
        background: #fff;
        display: flex; flex-direction: column;
    }
    
    .sidebar-header {
        padding: 15px;
        border-bottom: 1px solid #efefef;
        background: #fff;
        position: sticky; top: 0; z-index: 10;
        display: flex; flex-direction: column; gap: 10px;
    }

    .search-box {
        background: #efefef;
        border-radius: 8px;
        padding: 5px 10px;
        display: flex; align-items: center;
    }
    .search-box input {
        border: none; background: transparent;
        width: 100%; outline: none; font-size: 14px; margin-left: 8px;
    }

    .user-item { 
        padding: 12px 20px; 
        display: flex; align-items: center; 
        text-decoration: none; color: #262626; 
        transition: 0.2s;
        border-bottom: 1px solid #fafafa;
        position: relative; 
    }
    .user-item:hover { background: #fafafa; }
    .user-item.active { background: #efefef; }
    
    .user-item img { 
        width: 50px; height: 50px; 
        border-radius: 50%; margin-right: 15px; 
        object-fit: cover; border: 1px solid #eee;
    }

    .notification-badge {
        background-color: #ff3b30; color: white; border-radius: 10px; 
        padding: 2px 8px; font-size: 11px; font-weight: bold;
        margin-left: auto; display: none; 
    }

    /* Chat Area */
    .chat-area { flex: 1; display: flex; flex-direction: column; background: #fff; }
    .chat-header { padding: 10px 20px; border-bottom: 1px solid #dbdbdb; display: flex; align-items: center; background: #fff; height: 70px;}
    .msg-body { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; background-color: #fff; scroll-behavior: smooth; }
    
    .bubble { max-width: 65%; padding: 10px 16px; border-radius: 22px; font-size: 14.5px; line-height: 1.4; word-wrap: break-word; position: relative; }
    .incoming { align-self: flex-start; background: #efefef; color: #262626; border-bottom-left-radius: 4px; }
    .outgoing { align-self: flex-end; background: #0095f6; color: #fff; border-bottom-right-radius: 4px; }
    
    .chat-image {
        max-width: 100%; border-radius: 15px; margin-top: 5px; cursor: pointer;
    }

    .chat-input-area { padding: 15px 20px; border-top: 1px solid #dbdbdb; background: #fff; }
    .custom-input { background: #efefef; border: 1px solid transparent; border-radius: 20px; padding: 10px 20px; }
    .custom-input:focus { background: #fff; border-color: #dbdbdb; box-shadow: none; }
</style>

<div class="container col-10 msg-container"> 
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <div class="fw-bold text-center" style="font-size: 1.1rem;">
                <?= $user['username'] ?>
            </div>
            <div class="search-box">
                <i class="bi bi-search text-muted"></i>
                <input type="text" id="search_input" placeholder="Tìm kiếm...">
            </div>
        </div>
        
        <div id="user_list">
        <?php 
        if (is_array($chat_users) || is_object($chat_users)) {
            foreach($chat_users as $cu): 
                $u_info = getUser($cu['user_id']); 
                if (!$u_info) continue; 
                $is_active = ($chat_with_id == $u_info['id']) ? 'active' : '';
        ?>
            <a href="?message&uid=<?= $u_info['id'] ?>" class="user-item <?= $is_active ?>" id="user-sidebar-<?= $u_info['id'] ?>">
                <img src="assets/images/profile/<?= $u_info['profile_pic'] ?>">
                <div class="d-flex flex-column justify-content-center">
                    <div class="fw-bold user-name" style="font-size: 14px;"><?= $u_info['first_name'] ?> <?= $u_info['last_name'] ?></div>
                    <small class="text-muted" style="font-size: 12px;">Nhấn để chat</small>
                </div>
                <span class="notification-badge" id="badge-<?= $u_info['id'] ?>">0</span>
            </a>
        <?php 
            endforeach; 
        } else {
            echo '<div class="text-center mt-5 text-muted">Chưa theo dõi ai</div>';
        }
        ?>
        </div>
    </div>

    <div class="chat-area">
        <?php if($chat_with_user): ?>
            <div class="chat-header">
                <img src="assets/images/profile/<?= $chat_with_user['profile_pic'] ?>" class="rounded-circle border me-2" width="40" height="40" style="object-fit: cover;">
                <div>
                    <span class="fw-bold d-block"><?= $chat_with_user['first_name'] ?> <?= $chat_with_user['last_name'] ?></span>
                    <small class="text-muted" style="font-size: 12px;">Đang hoạt động</small>
                </div>
                
                <div class="ms-auto dropdown">
                    <a class="text-dark text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical h4"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?u=<?= $chat_with_user['username'] ?>">Xem trang cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="#" id="btnDeleteChat">
                                <i class="bi bi-trash3"></i> Xóa cuộc trò chuyện
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="msg-body" id="msgBody">
                <div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>
            </div>

            <div class="chat-input-area">
                <form id="chatForm" class="d-flex gap-2 align-items-center">
                    <label for="msg_image_input" class="text-muted" style="cursor: pointer;" title="Gửi ảnh">
                        <i class="bi bi-image h4 mb-0"></i>
                    </label>
                    <input type="file" id="msg_image_input" accept="image/*" hidden>
                    
                    <span id="file_name_display" class="badge bg-primary rounded-pill" style="display:none; max-width: 100px; overflow: hidden;"></span>

                    <input type="text" id="msg_text" class="form-control custom-input" placeholder="Nhắn tin..." autocomplete="off">
                    <button class="btn text-primary fw-bold" type="submit">Gửi</button>
                </form>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                <img src="assets/images/instagenz.png" alt="Logo" style="height: 100px; width: auto; object-fit: contain;">
                <h3>Tin nhắn của bạn</h3>
                <p class="text-muted">Gửi ảnh và tin nhắn riêng tư cho bạn bè.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var to_user_id = '<?= $chat_with_id ?>';
    var isFirstLoad = true;

    $(document).ready(function(){
        if(to_user_id){
            loadMessages();
            setInterval(loadMessages, 1000);
        }
        checkNotifications();
        setInterval(checkNotifications, 2000);

        // Hiển thị tên ảnh khi chọn
        $('#msg_image_input').change(function(){
            var fileName = this.files[0] ? this.files[0].name : '';
            if(fileName){
                $('#file_name_display').text(fileName).show();
            } else {
                $('#file_name_display').hide();
            }
        });

        // Xử lý nút Xóa Chat
        $('#btnDeleteChat').click(function(e){
            e.preventDefault();
            if(confirm('Bạn chắc chắn muốn xóa toàn bộ tin nhắn với người này chứ?')){
                $.ajax({
                    url: 'assets/php/ajax.php?delete_chat',
                    method: 'POST',
                    dataType: 'json',
                    data: { to_user_id: to_user_id },
                    success: function(response){
                        if(response.status){
                            window.location.href = '?message'; // Reload về trang trắng
                        } else {
                            alert('Lỗi khi xóa!');
                        }
                    }
                });
            }
        });
    });

    $('#chatForm').submit(function(e){
        e.preventDefault(); 
        var msg = $('#msg_text').val();
        var file_data = $('#msg_image_input').prop('files')[0];

        if(msg.trim() == '' && !file_data) return; 

        // Dùng FormData để gửi file
        var form_data = new FormData();
        form_data.append('to_user_id', to_user_id);
        form_data.append('msg', msg);
        if(file_data) form_data.append('msg_image', file_data);

        // Reset trước để giao diện mượt
        $('#msg_text').val('');
        $('#msg_image_input').val('');
        $('#file_name_display').hide();

        $.ajax({
            url: 'assets/php/ajax.php?sendmessage',
            method: 'POST',
            dataType: 'json',
            cache: false, contentType: false, processData: false,
            data: form_data,
            success: function(response){
                if(response.status){
                    loadMessages(true);
                } else {
                    alert('Lỗi: ' + response.msg);
                }
            }
        });
    });

    function loadMessages(forceScroll = false){
        $.ajax({
            url: 'assets/php/ajax.php?getmessages',
            method: 'POST',
            data: { to_user_id: to_user_id },
            success: function(data){
                var msgBody = document.getElementById("msgBody");
                var isAtBottom = (msgBody.scrollHeight - msgBody.scrollTop - msgBody.clientHeight) < 100;
                $('#msgBody').html(data);
                if(isFirstLoad || forceScroll || isAtBottom){
                    msgBody.scrollTop = msgBody.scrollHeight;
                    isFirstLoad = false;
                }
            }
        });
    }

    function checkNotifications() {
        $.ajax({
            url: 'assets/php/ajax.php?check_notifications',
            method: 'POST',
            dataType: 'json',
            success: function(data) {
                $('.notification-badge').hide();
                if (data && data.length > 0) {
                    data.forEach(function(item) {
                        if (item.from_user_id != to_user_id) {
                            $('#badge-' + item.from_user_id).text(item.unread_count).fadeIn();
                        }
                    });
                }
            }
        });
    }
</script>