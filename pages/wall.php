<?php
global $user;
global $posts; 
if (!isset($posts)) { $posts = filterPosts(); }
?>
<link rel="stylesheet" href="assets/css/wallstyle.css">

<div class="container col-9 rounded-0 d-flex justify-content-between">
    <div class="col-4 mt-4 p-3">
        <?php require_once 'pages/sidebar.php'; ?>
    </div>

    <div class="col-7 mt-4">
        <?php if (count($posts) < 1) { ?>
            <div class="card shadow-sm text-center p-5">
                <h5 class="mt-3">Bảng tin đang trống!</h5>
                <p class="text-muted">Hãy Follow mọi người để thấy bài viết.</p>
            </div>
        <?php } ?>
        
        <?php foreach ($posts as $post) {
            $likes = getLikes($post['id']);
            $comments = getComments($post['id']);
            
            // Check trạng thái Like
            $is_liked = false;
            if(function_exists('checkLikeStatus')){
                $is_liked = checkLikeStatus($post['id']);
            }
        ?>
            <div class="card mt-4 shadow-sm" data-post-id="<?= $post['id'] ?>">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="?u=<?= $post['username'] ?>" class="text-decoration-none text-dark">
                            <img src="assets/images/profile/<?= $post['profile_pic'] ?>" height="40" width="40" class="rounded-circle border" style="object-fit:cover;">
                        </a>
                        &nbsp;&nbsp;
                        <div>
                            <a href="?u=<?= $post['username'] ?>" class="text-decoration-none text-dark">
                                <h6 class="mb-0"><?= $post['first_name'] ?> <?= $post['last_name'] ?></h6>
                            </a>
                            <small class="text-muted" style="font-size: 12px;">@<?= $post['username'] ?> • <?= show_time_ago($post['created_at']) ?></small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <span href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </span>
                        <ul class="dropdown-menu">
                            <?php if ($post['user_id'] == $user['id']) { ?>
                                <li>
                                    <a class="dropdown-item text-danger delete_post_btn" href="#" data-post-id="<?= $post['id'] ?>">
                                        <i class="bi bi-trash"></i> Xóa bài viết
                                    </a>
                                </li>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="#">Báo cáo</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <?php if ($post['post_img']): ?>
                    <img src="assets/images/posts/<?= $post['post_img'] ?>" 
                         class="card-img-top show_post_modal" 
                         data-post-id="<?= $post['id'] ?>"
                         style="object-fit:cover; cursor:pointer;">
                <?php endif; ?>

                <div class="card-body">
                    <?php if ($post['post_text']): ?><p class="card-text"><?= $post['post_text'] ?></p><?php endif; ?>

                    <div class="d-flex align-items-center mt-3 border-top pt-2">
                        <span class="me-3">
                            <i class="bi bi-heart-fill text-danger unlike_btn <?= $is_liked ? '' : 'd-none' ?>" 
                               style="cursor:pointer; font-size:1.5rem;" data-post-id="<?= $post['id'] ?>"></i>
                            
                            <i class="bi bi-heart like_btn <?= $is_liked ? 'd-none' : '' ?>" 
                               style="cursor:pointer; font-size:1.5rem;" data-post-id="<?= $post['id'] ?>"></i>
                            
                            <span class="ms-2 fw-bold show_likes" data-post-id="<?= $post['id'] ?>">
                                <?= count($likes) ?>
                            </span>
                        </span>
                        
                        <span class="ms-3 text-muted" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#comments-<?= $post['id'] ?>">
                            <i class="bi bi-chat-left" style="font-size:1.5rem;"></i> 
                            <span class="ms-1 comment-count" data-post-id="<?= $post['id'] ?>"><?= count($comments) ?></span>
                        </span>
                    </div>

                    <div class="collapse mt-2" id="comments-<?= $post['id'] ?>">
                        <div class="comment-list" id="comment-list-<?= $post['id'] ?>" style="max-height: 200px; overflow-y: auto;">
                            <?php 
                            // Chỉ hiển thị comments gốc (không có parent_id hoặc parent_id = 0)
                            $parent_comments = array_filter($comments, function($c) {
                                return !isset($c['parent_id']) || $c['parent_id'] == 0;
                            });
                            foreach ($parent_comments as $comment): 
                                $cuser = getUser($comment['user_id']);
                                $comment_text = htmlspecialchars($comment['comment']);
                                // Xử lý @mention trong comment
                                $comment_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $comment_text);
                                
                                // Lấy replies của comment này
                                $replies = getComments($post['id'], $comment['id']);
                            ?>
                                <div class="comment-item" data-comment-id="<?= $comment['id'] ?>">
                                    <div class="d-flex align-items-start p-2">
                                        <img src="assets/images/profile/<?= $cuser['profile_pic'] ?>" class="rounded-circle border" style="width:35px; height:35px; object-fit:cover">
                                        <div class="ms-2 flex-grow-1">
                                            <div class="bg-light p-2 rounded">
                                                <span class="fw-bold small"><?= htmlspecialchars($cuser['first_name'] . ' ' . $cuser['last_name']) ?></span>
                                                <span class="comment-text"><?= $comment_text ?></span>
                                            </div>
                                            <div class="mt-1">
                                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" style="font-size: 12px;">Trả lời</button>
                                                <?php if (count($replies) > 0): ?>
                                                    <button class="btn btn-sm btn-link p-0 text-muted show-replies-btn" data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" style="font-size: 12px;">
                                                        <span class="replies-count-<?= $comment['id'] ?>"><?= count($replies) ?></span> trả lời
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="reply-container-<?= $comment['id'] ?> comment-replies-level-1">
                                        <?php if (count($replies) > 0): ?>
                                            <?php foreach ($replies as $reply): 
                                                $ruser = getUser($reply['user_id']);
                                                $reply_text = htmlspecialchars($reply['comment']);
                                                $reply_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $reply_text);
                                                
                                                // Lấy nested replies của reply này
                                                $nested_replies = getComments($post['id'], $reply['id']);
                                            ?>
                                                <div class="comment-reply-wrapper comment-level-1" data-comment-id="<?= $reply['id'] ?>">
                                                    <div class="d-flex align-items-start">
                                                        <div class="comment-thread-line"></div>
                                                        <img src="assets/images/profile/<?= $ruser['profile_pic'] ?>" class="rounded-circle border" style="width:32px; height:32px; object-fit:cover">
                                                        <div class="ms-2 flex-grow-1">
                                                            <div class="bg-light p-2 rounded comment-content-wrapper">
                                                                <span class="fw-bold"><?= htmlspecialchars($ruser['first_name'] . ' ' . $ruser['last_name']) ?></span>
                                                                <span><?= $reply_text ?></span>
                                                            </div>
                                                            <div class="mt-1">
                                                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="<?= $reply['id'] ?>" data-post-id="<?= $post['id'] ?>" style="font-size: 12px;">Trả lời</button>
                                                            </div>
                                                            <div class="reply-container-<?= $reply['id'] ?> comment-replies-level-2">
                                                                <?php if (count($nested_replies) > 0): ?>
                                                                    <?php foreach ($nested_replies as $nested_reply): 
                                                                        $nuser = getUser($nested_reply['user_id']);
                                                                        $nested_reply_text = htmlspecialchars($nested_reply['comment']);
                                                                        $nested_reply_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $nested_reply_text);
                                                                        
                                                                        // Lấy nested replies của nested reply này (level 3+)
                                                                        $deep_nested_replies = getComments($post['id'], $nested_reply['id']);
                                                                    ?>
                                                                        <div class="comment-nested-reply comment-level-2" data-comment-id="<?= $nested_reply['id'] ?>">
                                                                            <div class="d-flex align-items-start">
                                                                                <div class="comment-thread-line-nested"></div>
                                                                                <img src="assets/images/profile/<?= $nuser['profile_pic'] ?>" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                                                                                <div class="ms-2 flex-grow-1">
                                                                                    <div class="bg-light p-2 rounded comment-content-wrapper">
                                                                                        <span class="fw-bold"><?= htmlspecialchars($nuser['first_name'] . ' ' . $nuser['last_name']) ?></span>
                                                                                        <span><?= $nested_reply_text ?></span>
                                                                                    </div>
                                                                                    <div class="mt-1">
                                                                                        <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="<?= $nested_reply['id'] ?>" data-post-id="<?= $post['id'] ?>" style="font-size: 11px;">Trả lời</button>
                                                                                    </div>
                                                                                    <?php if (count($deep_nested_replies) > 0): ?>
                                                                                        <div class="reply-container-<?= $nested_reply['id'] ?> comment-replies-level-3">
                                                                                            <?php foreach ($deep_nested_replies as $deep_reply): 
                                                                                                $duser = getUser($deep_reply['user_id']);
                                                                                                $deep_reply_text = htmlspecialchars($deep_reply['comment']);
                                                                                                $deep_reply_text = preg_replace('/@(\w+)/', '<a href="?u=$1" class="text-primary fw-semibold">@$1</a>', $deep_reply_text);
                                                                                            ?>
                                                                                                <div class="comment-nested-reply comment-level-3" data-comment-id="<?= $deep_reply['id'] ?>">
                                                                                                    <div class="d-flex align-items-start">
                                                                                                        <div class="comment-thread-line-deep"></div>
                                                                                                        <img src="assets/images/profile/<?= $duser['profile_pic'] ?>" class="rounded-circle border" style="width:28px; height:28px; object-fit:cover">
                                                                                                        <div class="ms-2 flex-grow-1">
                                                                                                            <div class="bg-light p-2 rounded comment-content-wrapper">
                                                                                                                <span class="fw-bold"><?= htmlspecialchars($duser['first_name'] . ' ' . $duser['last_name']) ?></span>
                                                                                                                <span><?= $deep_reply_text ?></span>
                                                                                                            </div>
                                                                                                            <div class="mt-1">
                                                                                                                <button class="btn btn-sm btn-link p-0 text-muted reply-btn" data-comment-id="<?= $deep_reply['id'] ?>" data-post-id="<?= $post['id'] ?>" style="font-size: 11px;">Trả lời</button>
                                                                                                            </div>
                                                                                                            <div class="reply-container-<?= $deep_reply['id'] ?>"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php endforeach; ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="reply-container-<?= $nested_reply['id'] ?>"></div>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-2 position-relative">
                            <input type="text" class="form-control rounded-0 border-0 comment-input" placeholder="Thêm bình luận..." data-post-id="<?= $post['id'] ?>" data-parent-id="0">
                            <div class="mention-autocomplete" id="mention-autocomplete-<?= $post['id'] ?>" style="display:none;"></div>
                            <button class="btn btn-outline-primary rounded-0 border-0 add-comment-btn" data-post-id="<?= $post['id'] ?>" data-parent-id="0">Đăng</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // === 1. XỬ LÝ LIKE ===
    // (Ajax mới của bạn dùng ?like)
    $(document).off('click', '.like_btn').on('click', '.like_btn', function() {
        var post_id = $(this).data('post-id');
        var btn = $(this);
        var icon_fill = btn.siblings('.unlike_btn');
        var count_span = btn.siblings('.show_likes');

        // Giao diện: Ẩn tim trắng, hiện tim đỏ, +1 số lượng
        btn.addClass('d-none');
        icon_fill.removeClass('d-none');
        var current_count = parseInt(count_span.text());
        count_span.text(current_count + 1);

        $.ajax({
            url: 'assets/php/ajax.php?like=1', // Đã đổi thành ?like theo code mới
            method: 'POST',
            data: { post_id: post_id },
            dataType: 'json',
            success: function(response) {
                if(!response.status){
                    alert(response.msg);
                    // Rollback nếu lỗi
                    btn.removeClass('d-none');
                    icon_fill.addClass('d-none');
                    count_span.text(current_count);
                }
            }
        });
    });

    // === 2. XỬ LÝ UNLIKE ===
    // (Ajax mới của bạn dùng ?unlike hoặc ?like cũng được vì nó toggle, nhưng dùng ?unlike cho rõ ràng)
    $(document).off('click', '.unlike_btn').on('click', '.unlike_btn', function() {
        var post_id = $(this).data('post-id');
        var btn = $(this);
        var icon_empty = btn.siblings('.like_btn');
        var count_span = btn.siblings('.show_likes');

        // Giao diện: Ẩn tim đỏ, hiện tim trắng, -1 số lượng
        btn.addClass('d-none');
        icon_empty.removeClass('d-none');
        var current_count = parseInt(count_span.text());
        if(current_count > 0) count_span.text(current_count - 1);

        $.ajax({
            url: 'assets/php/ajax.php?unlike=1', // Đã đổi thành ?unlike
            method: 'POST',
            data: { post_id: post_id },
            dataType: 'json',
            success: function(response) {
                if(!response.status){
                    alert('Lỗi kết nối');
                }
            }
        });
    });

    // === 3. XỬ LÝ COMMENT ===
    $(document).off('click', '.add-comment-btn').on('click', '.add-comment-btn', function() {
        var post_id = $(this).data('post-id');
        var parent_id = $(this).data('parent-id') || 0;
        var input = $(this).siblings('.comment-input').first();
        var content = input.val().trim();
        var mentioned_user_id = input.data('mentioned-user-id') || 0;

        if(content == '') return;

        var $this = $(this);
        var list = parent_id > 0 ? $('.reply-container-' + parent_id) : $('#comment-list-' + post_id);
        var count_span = $('.comment-count[data-post-id="'+post_id+'"]');

        $this.attr('disabled', true);
        input.attr('disabled', true);

        $.ajax({
            url: 'assets/php/ajax.php?addcomment=1',
            method: 'POST',
            dataType: 'json',
            data: { 
                post_id: post_id, 
                comment: content,
                parent_id: parent_id,
                mentioned_user_id: mentioned_user_id
            },
            success: function(resp){
                if(resp.status){
                    input.val('');
                    input.data('mentioned-user-id', 0);
                    $('.mention-autocomplete').hide();
                    
                    if (parent_id > 0) {
                        // Nếu là reply, thêm vào container reply
                        list.append(resp.comment);
                    } else {
                        // Nếu là comment gốc, thêm vào list
                    list.append(resp.comment); 
                    }
                    
                    list.scrollTop(list[0].scrollHeight);
                    
                    // Tăng số lượng comment
                    if (count_span.length) {
                        var current_c = parseInt(count_span.text()) || 0;
                    count_span.text(current_c + 1);
                    }
                } else {
                    alert('Lỗi bình luận');
                }
                $this.attr('disabled', false);
                input.attr('disabled', false);
            },
            error: function() {
                $this.attr('disabled', false);
                input.attr('disabled', false);
            }
        });
    });

    // === 4. XỬ LÝ REPLY BUTTON ===
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        var comment_id = $(this).data('comment-id');
        var post_id = $(this).data('post-id');
        var container = $('.reply-container-' + comment_id);
        
        // Ẩn tất cả các reply input khác
        $('.reply-input-container').remove();
        
        // Tạo input reply
        var replyHtml = '<div class="reply-input-container mt-2 ms-5 position-relative">' +
            '<input type="text" class="form-control comment-input" placeholder="Trả lời..." data-post-id="' + post_id + '" data-parent-id="' + comment_id + '">' +
            '<div class="mention-autocomplete"></div>' +
            '<button class="btn btn-sm btn-primary mt-1 add-comment-btn" data-post-id="' + post_id + '" data-parent-id="' + comment_id + '">Trả lời</button>' +
            '<button class="btn btn-sm btn-secondary mt-1 ms-1 cancel-reply-btn">Hủy</button>' +
            '</div>';
        
        container.append(replyHtml);
        container.find('.comment-input').focus();
    });

    // Hủy reply
    $(document).on('click', '.cancel-reply-btn', function() {
        $(this).closest('.reply-input-container').remove();
    });

    // === 5. XỬ LÝ @MENTION AUTOCOMPLETE ===
    var mentionTimeout = null;
    $(document).on('input', '.comment-input', function() {
        var $input = $(this);
        var value = $input.val();
        var cursorPos = $input[0].selectionStart;
        var textBeforeCursor = value.substring(0, cursorPos);
        var atIndex = textBeforeCursor.lastIndexOf('@');
        
        if (atIndex !== -1) {
            var searchTerm = textBeforeCursor.substring(atIndex + 1).trim();
            // Chỉ search nếu sau @ có ít nhất 1 ký tự và không có khoảng trắng
            if (searchTerm.length > 0 && !/\s/.test(searchTerm)) {
                clearTimeout(mentionTimeout);
                mentionTimeout = setTimeout(function() {
                    var post_id = $input.data('post-id');
                    $.ajax({
                        url: 'assets/php/ajax.php?search_following',
                        method: 'POST',
                        dataType: 'json',
                        data: { search: searchTerm },
                        success: function(response) {
                            if (response.status && response.users.length > 0) {
                                var $autocomplete = $input.siblings('.mention-autocomplete');
                                if ($autocomplete.length == 0) {
                                    $autocomplete = $input.closest('.position-relative').find('.mention-autocomplete');
                                }
                                
                                var html = '<div class="list-group position-absolute" style="z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:white; border:1px solid #ddd; border-radius:4px;">';
                                response.users.forEach(function(user) {
                                    html += '<a href="javascript:void(0);" class="list-group-item list-group-item-action mention-item" data-username="' + user.username + '" data-user-id="' + user.id + '">' +
                                        '<div class="d-flex align-items-center">' +
                                        '<img src="assets/images/profile/' + user.profile_pic + '" class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover">' +
                                        '<div><strong>' + user.first_name + ' ' + user.last_name + '</strong><br><small>@' + user.username + '</small></div>' +
                                        '</div></a>';
                                });
                                html += '</div>';
                                
                                $autocomplete.html(html).show();
                            } else {
                                $input.siblings('.mention-autocomplete').hide();
                            }
                        }
                    });
                }, 300);
            } else {
                $input.siblings('.mention-autocomplete').hide();
            }
        } else {
            $input.siblings('.mention-autocomplete').hide();
        }
    });

    // Chọn mention từ autocomplete
    $(document).on('click', '.mention-item', function() {
        var $input = $(this).closest('.position-relative').find('.comment-input');
        if ($input.length == 0) {
            $input = $(this).closest('.reply-input-container').find('.comment-input');
        }
        
        var username = $(this).data('username');
        var user_id = $(this).data('user-id');
        var value = $input.val();
        var cursorPos = $input[0].selectionStart;
        var textBeforeCursor = value.substring(0, cursorPos);
        var textAfterCursor = value.substring(cursorPos);
        var atIndex = textBeforeCursor.lastIndexOf('@');
        
        if (atIndex !== -1) {
            var newValue = textBeforeCursor.substring(0, atIndex) + '@' + username + ' ' + textAfterCursor;
            $input.val(newValue);
            $input.data('mentioned-user-id', user_id);
            $input[0].selectionStart = $input[0].selectionEnd = atIndex + username.length + 2;
            $input.focus();
        }
        
        $(this).closest('.mention-autocomplete').hide();
    });

    // Ẩn autocomplete khi click ra ngoài
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.comment-input, .mention-autocomplete').length) {
            $('.mention-autocomplete').hide();
        }
    });

    // Phím Enter để comment - Prevent default để không xuống dòng
    $(document).on('keydown', '.comment-input', function(e) {
        if(e.which == 13 || e.keyCode == 13) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this).siblings('.add-comment-btn').first();
            if ($btn.length > 0 && !$btn.prop('disabled')) {
                $btn.click();
            }
        }
    });


    function syncUpdates(){
        var pids = [];
        $('.card[data-post-id]').each(function(){ pids.push($(this).data('post-id')); });
        if(pids.length == 0) return;

        $.ajax({
            url: 'assets/php/ajax.php?get_post_updates',
            method: 'POST',
            data: { post_ids: pids },
            dataType: 'json',
            success: function(resp){
                if(resp.status){
                    $.each(resp.data, function(id, val){
                        $('.show_likes[data-post-id="'+id+'"]').text(val.likes);
                        $('.comment-count[data-post-id="'+id+'"]').text(val.comments);
                    });
                }
            }
        });
    }
});
</script>