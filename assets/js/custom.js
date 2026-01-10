// Preview image khi chọn file trong form đăng bài
var input_post = document.querySelector("#select_post_img");
if (input_post) {
    input_post.addEventListener("change", function() {
        previewPostImage(this);
    });
}

// Preview image khi chọn file trong form profile
var input_profile = document.querySelector("#formFile");
if (input_profile) {
    input_profile.addEventListener("change", function() {
        previewImage(this, "#profile_img_pre");
    });
}

// Hàm preview image cho post
function previewPostImage(input) {
    var fileObject = input.files[0];
    
    if (!fileObject) {
        // Nếu không có file, ẩn preview
        document.getElementById('post_img_preview_container').style.display = 'none';
        return;
    }
    
    // Kiểm tra file có phải là image không
    if (!fileObject.type.match('image.*')) {
        alert('Vui lòng chọn file hình ảnh!');
        input.value = '';
        return;
    }
    
    // Kiểm tra kích thước file (max 10MB)
    if (fileObject.size > 10 * 1024 * 1024) {
        alert('File quá lớn! Vui lòng chọn file nhỏ hơn 10MB.');
        input.value = '';
        return;
    }
    
    var fileReader = new FileReader();

    fileReader.readAsDataURL(fileObject);

    fileReader.onload = function() {
        var result = fileReader.result;
        var previewContainer = document.getElementById('post_img_preview_container');
        var previewImg = document.getElementById('post_img_preview');

        previewImg.setAttribute("src", result);
        previewContainer.style.display = 'block';
        
        // Scroll đến preview để user thấy ngay
        previewContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    fileReader.onerror = function() {
        alert('Lỗi khi đọc file! Vui lòng thử lại.');
        input.value = '';
    }
}

// Xóa preview image
$(document).on('click', '#remove_preview_btn', function() {
    var input = document.getElementById('select_post_img');
    if (input) {
        input.value = '';
    }
    document.getElementById('post_img_preview_container').style.display = 'none';
    document.getElementById('post_img_preview').setAttribute('src', '');
});

// Reset form khi đóng modal
$('#addpost').on('hidden.bs.modal', function () {
    // Reset form
    var form = document.getElementById('add_post_form');
    if (form) {
        form.reset();
    }
    // Ẩn preview
    document.getElementById('post_img_preview_container').style.display = 'none';
    document.getElementById('post_img_preview').setAttribute('src', '');
});

// Hàm preview image chung (cho profile, etc.)
function previewImage(input, img_selector) {
    var fileObject = input.files[0];
    
    if (!fileObject) {
        return;
    }
    
    var fileReader = new FileReader();

    fileReader.readAsDataURL(fileObject);

    fileReader.onload = function() {
        var result = fileReader.result;
        var img = document.querySelector(img_selector);

        if (img) {
            img.setAttribute("src", result);
            img.style.display = "block";
        }
    }
}
$(".add-comment").click(function(){
    var button = this;

    var comment_v = $(button).siblings('.comment-input').val();
    if(comment_v==''){
        return 0;
    }
    var post_id_v = $(this).data('postId');
    var cs = $(this).data('cs');
    var page = $(this).data('page');
    $(button).attr('disabled', true);
    $(button).siblings('.comment-input').attr('disabled', true);
    $.ajax({
        url:'assets/php/ajax.php?addcomment',
        method: 'post',
        dataType: 'json',
        data: {post_id: post_id_v, comment: comment_v},
        success: function(response){

            if(response.status){
                $(button).attr('disabled',false);
                $(button).siblings('.comment-input').attr('disabled', false);
                $(button).siblings('.comment-input').val('');
                $("#" + cs).append(response.comment);
                $('.nce').hide();
                if(page='wall'){
                    location.reload();
                }

            }else{
                $(button).attr('disabled',false);
                $(button).siblings('.comment-input').attr('disabled', false);

                alert('something is wrong, try again after some time');
            }
        }
    });
});
$(document).on('click', '.like_btn', function(){
    var post_id_v = $(this).data('postId');
    var button = this;
    $(button).hide();
    $(button).siblings('.unlike_btn').show();
    var count_element = $(button).siblings('.show_likes');
    var current_count = parseInt(count_element.text());
    if(isNaN(current_count)) current_count = 0;
    
    count_element.text(current_count + 1);
    $.ajax({
        url: 'assets/php/ajax.php?like',
        method: 'post',
        dataType: 'json',
        data: { post_id: post_id_v },
        success: function(response){
            if(response.status){
            } else {
                $(button).show();
                $(button).siblings('.unlike_btn').hide();
                count_element.text(current_count);
                alert('Có lỗi xảy ra!');
            }
        }
    });
});

$(document).on('click', '.unlike_btn', function(){
    var post_id_v = $(this).data('postId');
    var button = this;
    $(button).hide();
    $(button).siblings('.like_btn').show();
    var count_element = $(button).siblings('.show_likes'); 
    var current_count = parseInt(count_element.text()); // Lấy số hiện tại
    count_element.text(current_count - 1); // Trừ đi 1
    $.ajax({
        url: 'assets/php/ajax.php?unlike',
        method: 'post',
        dataType: 'json',
        data: { post_id: post_id_v },
        success: function(response){
            if(response.status){
            } else {
                $(button).show();
                $(button).siblings('.like_btn').hide();
                count_element.text(current_count); // Trả lại số cũ
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            }
        }
    });
});

$(document).on('click', '.add-comment-btn', function(){
    var post_id = $(this).data('postId');
    var button = this;
    var input = $(button).siblings('.comment-input'); 
    var comment_text = input.val();

    if(comment_text == ''){
        return; // Nếu rỗng thì không làm gì
    }
    $(button).attr('disabled', true);
    input.attr('disabled', true);

    $.ajax({
        url: 'assets/php/ajax.php?addcomment',
        method: 'post',
        dataType: 'json',
        data: { post_id: post_id, comment: comment_text },
        success: function(response){
            if(response.status){
                $(button).attr('disabled', false);
                input.attr('disabled', false);
                input.val(''); // Xóa trắng ô nhập
                $("#comment-list-" + post_id).append(response.comment);
            }else{
                alert('Lỗi đăng bình luận');
                $(button).attr('disabled', false);
                input.attr('disabled', false);
            }
        }
    });
});

$(document).on('click', '.show_likes', function(){
    var post_id = $(this).data('postId');

    $('#likesModal').modal('show');
    $('#likesModalBody').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
    
    $.ajax({
        url: 'assets/php/ajax.php?get_likes',
        method:'post',
        dataType: 'json',
        data: {post_id: post_id},
        success:function(response){
            if(response.status){
                $('#likesModalBody').html(response.html);
            }
        }
    });
});

$(document).on('click', '.show_post_modal', function(){
    var post_id = $(this).data('post-id'); // Sửa lại thành 'post-id' cho chuẩn HTML data-post-id
    $('#popup_add_comment_btn').data('post-id', post_id);
    $('#popup_like_btn').data('post-id', post_id);
    $('#popup_unlike_btn').data('post-id', post_id);
    $('#postViewModal').modal('show');
    $('#popup_post_menu').html(''); 
    $('#popup_post_img').attr('src', '');
    
    // Khởi tạo real-time cho modal
    var modalLastCommentId = 0;
    var modalRealtimeInterval = null;
    
    // Dừng interval cũ nếu có
    if (window.modalRealtimeInterval) {
        clearInterval(window.modalRealtimeInterval);
    }
    
    // Hàm cập nhật real-time cho modal
    function syncModalRealtime() {
        var currentPostId = $('#popup_like_btn').data('post-id');
        if (!currentPostId || !$('#postViewModal').hasClass('show')) {
            if (window.modalRealtimeInterval) {
                clearInterval(window.modalRealtimeInterval);
                window.modalRealtimeInterval = null;
            }
            return;
        }
        
        $.ajax({
            url: 'assets/php/ajax.php?get_realtime_updates',
            method: 'POST',
            dataType: 'json',
            data: { 
                post_ids: [currentPostId],
                last_comment_ids: {[currentPostId]: modalLastCommentId},
                last_update_time: 0
            },
            success: function(resp){
                if(resp.status && resp.updates && resp.updates.post_updates && resp.updates.post_updates[currentPostId]){
                    var update = resp.updates.post_updates[currentPostId];
                    
                    // Cập nhật số lượng likes
                    if (update.likes_count > 0) {
                        $('#popup_like_count').text(update.likes_count + ' likes');
                    } else {
                        $('#popup_like_count').text('Be the first to like this');
                    }
                    
                    // Cập nhật trạng thái like/unlike
                    if (update.is_liked) {
                        $('#popup_unlike_btn').show();
                        $('#popup_like_btn').hide();
                    } else {
                        $('#popup_unlike_btn').hide();
                        $('#popup_like_btn').show();
                    }
                    
                    // Thêm comments mới
                    if (update.new_comments && update.new_comments.length > 0) {
                        var $popupCommentList = $('#popup_comment_list');
                        
                        $.each(update.new_comments, function(idx, newComment){
                            var $existingComment = $('[data-comment-id="' + newComment.id + '"]');
                            if ($existingComment.length == 0) {
                                // Sử dụng html_popup cho modal
                                var htmlToAdd = newComment.html_popup || newComment.html;
                                
                                if (newComment.parent_id > 0) {
                                    // Đây là reply - thêm vào container reply
                                    var $replyContainer = $('.reply-container-popup-' + newComment.parent_id);
                                    if ($replyContainer.length > 0) {
                                        $replyContainer.append(htmlToAdd);
                                    } else {
                                        // Nếu container chưa có, tìm và tạo
                                        var $parentComment = $('.comment-item[data-comment-id="' + newComment.parent_id + '"], .comment-reply-wrapper[data-comment-id="' + newComment.parent_id + '"], .comment-nested-reply[data-comment-id="' + newComment.parent_id + '"]');
                                        if ($parentComment.length > 0) {
                                            var containerHtml = '<div class="reply-container-popup-' + newComment.parent_id + ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>';
                                            $parentComment.find('.ms-2, .flex-grow-1').last().append(containerHtml);
                                            $('.reply-container-popup-' + newComment.parent_id).append(htmlToAdd);
                                        } else {
                                            // Fallback: thêm vào comment list
                                            $popupCommentList.append(htmlToAdd);
                                        }
                                    }
                                } else {
                                    // Đây là comment gốc - thêm vào comment list
                                    $popupCommentList.append(htmlToAdd);
                                }
                                
                                // Scroll đến comment mới
                                setTimeout(function() {
                                    var $popupContainer = $('#popup_comment_list_container');
                                    if ($popupContainer.length > 0) {
                                        $popupContainer.scrollTop($popupContainer[0].scrollHeight);
                                    }
                                }, 100);
                            }
                        });
                        
                        // Cập nhật last comment id
                        if (update.last_comment_id > modalLastCommentId) {
                            modalLastCommentId = update.last_comment_id;
                        }
                    }
                }
            }
        });
    }
    
    $.ajax({
        url:'assets/php/ajax.php?get_post_view',
        method:'post',
        dataType: 'json',
        data: {post_id: post_id},
        success: function(response){
            if(response.status){
                $('#popup_post_img').attr('src', 'assets/images/posts/' + response.post_img);
                $('#popup_user_img').attr('src', 'assets/images/profile/' + response.profile_pic);
                
                $('#popup_user_name').text(response.fullname);
                $('#popup_username').text('@' + response.username);
                $('#popup_caption').text(response.post_text);
                $('#popup_posted_time').text(response.created_at);
                $('#popup_comment_list').html(response.comments_html);
                // Reset comment input
                $('#popup_comment_input').data('parent-id', 0);
                $('#popup_add_comment_btn').data('parent-id', 0);
                if(response.like_count > 0){
                    $('#popup_like_count').text(response.like_count + ' likes');
                } else {
                    $('#popup_like_count').text('Be the first to like this');
                }
                if(response.is_liked){
                    $('#popup_unlike_btn').show();
                    $('#popup_like_btn').hide();
                } else {
                    $('#popup_unlike_btn').hide();
                    $('#popup_like_btn').show();
                }
                
                // Khởi tạo modalLastCommentId từ comments hiện có
                var $lastComment = $('#popup_comment_list .comment-item, #popup_comment_list .comment-reply-wrapper, #popup_comment_list .comment-nested-reply').last();
                if ($lastComment.length > 0) {
                    modalLastCommentId = parseInt($lastComment.data('comment-id')) || 0;
                }
                
                var menu_html = '';
                
                if(response.is_own_post){
                    menu_html = `
                        <li>
                            <a class="dropdown-item edit_post_btn" href="#" 
                               data-bs-toggle="modal" data-bs-target="#edit_post_modal"
                               data-post-id="${response.id}"
                               data-post-text="${response.post_text}"
                               data-post-img="${response.post_img}">
                               <i class="bi bi-pencil-square"></i> Sửa bài viết
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger delete_post_btn" href="#" data-post-id="${response.id}">
                               <i class="bi bi-trash"></i> Xóa bài viết
                            </a>
                        </li>
                    `;
                } else {
                    var postUrl = 'http://localhost/instagenz/?post_view=' + response.id;
                    
                    menu_html = `
                        <li>
                            <a class="dropdown-item copy_link_btn" href="#" data-post-url="${postUrl}">
                                <i class="bi bi-clipboard"></i> Sao chép liên kết
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" 
                               data-bs-toggle="modal" data-bs-target="#report_post_modal" 
                               onclick="$('#report_post_id').val(${response.id})">
                                <i class="bi bi-flag"></i> Báo cáo
                            </a>
                        </li>
                    `;
                }
                $('#popup_post_menu').html(menu_html);
                
                // Bắt đầu real-time updates cho modal (mỗi 2 giây)
                if (window.modalRealtimeInterval) {
                    clearInterval(window.modalRealtimeInterval);
                }
                window.modalLastCommentId = modalLastCommentId;
                window.modalRealtimeInterval = setInterval(syncModalRealtime, 2000);
            }
        }
    });
    
    // Dừng real-time khi đóng modal
    $('#postViewModal').on('hidden.bs.modal', function () {
        if (window.modalRealtimeInterval) {
            clearInterval(window.modalRealtimeInterval);
            window.modalRealtimeInterval = null;
        }
    });
});
$('#popup_add_comment_btn').click(function(){
    var post_id = $('#popup_like_btn').data('post-id');
    var parent_id = $(this).data('parent-id') || 0;
    var comment_text = $('#popup_comment_input').val().trim();
    var mentioned_user_id = $('#popup_comment_input').data('mentioned-user-id') || 0;
    var button = this;

    if(comment_text == '') return;

    $(button).attr('disabled', true);
    $('#popup_comment_input').attr('disabled', true);

    $.ajax({
        url: 'assets/php/ajax.php?addcomment',
        method: 'post',
        dataType: 'json',
        data: {
            post_id: post_id, 
            comment: comment_text,
            parent_id: parent_id,
            mentioned_user_id: mentioned_user_id
        },
        success: function(response){
            if(response.status){
                // Xóa reply input container nếu có
                $('.reply-input-container-popup').remove();
                
                if (parent_id > 0) {
                    // Tìm container reply và thêm vào
                    var $replyContainer = $('.reply-container-popup-' + parent_id);
                    
                    // Nếu container không tồn tại, tìm và tạo mới
                    if ($replyContainer.length == 0) {
                        // Tìm element cha (comment-item, comment-reply-wrapper, hoặc comment-nested-reply)
                        var $parentElement = $('.comment-item[data-comment-id="' + parent_id + '"], .comment-reply-wrapper[data-comment-id="' + parent_id + '"], .comment-nested-reply[data-comment-id="' + parent_id + '"]');
                        if ($parentElement.length > 0) {
                            // Tạo container mới
                            var containerHtml = '<div class="reply-container-popup-' + parent_id + ' mt-2 ms-3" style="border-left: 2px solid #e9ecef; padding-left: 12px;"></div>';
                            $parentElement.find('.ms-2, .flex-grow-1').last().append(containerHtml);
                            $replyContainer = $('.reply-container-popup-' + parent_id);
                        }
                    }
                    
                    if ($replyContainer.length > 0) {
                        $replyContainer.append(response.comment);
                    } else {
                        // Fallback: thêm vào popup_comment_list
                        $('#popup_comment_list').append(response.comment);
                    }
                } else {
                    // Comment gốc
                    $('#popup_comment_list').append(response.comment);
                }
                
                $('#popup_comment_input').val('');
                $('#popup_comment_input').attr('placeholder', 'Add a comment...');
                $('#popup_comment_input').data('mentioned-user-id', 0);
                $('#popup_comment_input').data('parent-id', 0);
                $(button).data('parent-id', 0);
                $('#popup-mention-autocomplete').hide();
                $(button).attr('disabled', false);
                $('#popup_comment_input').attr('disabled', false);
                
                // Scroll to bottom
                var commentContainer = document.getElementById('popup_comment_list_container');
                if (commentContainer) {
                    setTimeout(function() {
                        commentContainer.scrollTop = commentContainer.scrollHeight;
                    }, 100);
                }
            }else{
                alert('Lỗi!');
                $(button).attr('disabled', false);
                $('#popup_comment_input').attr('disabled', false);
            }
        }
    });
});

// Xử lý reply trong modal
$(document).on('click', '.reply-btn-popup', function(e) {
    e.preventDefault();
    var comment_id = $(this).data('comment-id');
    var post_id = $(this).data('post-id');
    
    // Ẩn tất cả các reply input khác và reset input chính
    $('.reply-input-container-popup').remove();
    $('#popup_comment_input').data('parent-id', 0);
    $('#popup_add_comment_btn').data('parent-id', 0);
    
    // Set parent_id cho input chính
    $('#popup_comment_input').data('parent-id', comment_id);
    $('#popup_add_comment_btn').data('parent-id', comment_id);
    $('#popup_comment_input').attr('placeholder', 'Trả lời...');
    
    // Focus vào input chính để user có thể gõ ngay
    $('#popup_comment_input').focus();
    
    // Scroll đến input
    var commentContainer = document.getElementById('popup_comment_list_container');
    if (commentContainer) {
        setTimeout(function() {
            $('#popup_comment_input')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }
});

// Phím Enter để comment trong modal
$(document).on('keydown', '#popup_comment_input', function(e) {
    if(e.which == 13 || e.keyCode == 13) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $('#popup_add_comment_btn');
        if ($btn.length > 0 && !$btn.prop('disabled')) {
            $btn.click();
        }
    }
});

// Hủy reply trong modal
$(document).on('click', '.cancel-reply-popup', function() {
    $(this).closest('.reply-input-container-popup').remove();
    $('#popup_comment_input').data('parent-id', 0);
    $('#popup_add_comment_btn').data('parent-id', 0);
});

// Xử lý @mention trong modal
$('#popup_comment_input').on('input', function() {
    var $input = $(this);
    var value = $input.val();
    var cursorPos = $input[0].selectionStart;
    var textBeforeCursor = value.substring(0, cursorPos);
    var atIndex = textBeforeCursor.lastIndexOf('@');
    
    if (atIndex !== -1) {
        var searchTerm = textBeforeCursor.substring(atIndex + 1).trim();
        if (searchTerm.length > 0 && !/\s/.test(searchTerm)) {
            clearTimeout(window.mentionTimeout);
            window.mentionTimeout = setTimeout(function() {
                $.ajax({
                    url: 'assets/php/ajax.php?search_following',
                    method: 'POST',
                    dataType: 'json',
                    data: { search: searchTerm },
                    success: function(response) {
                        if (response.status && response.users.length > 0) {
                            var html = '<div class="list-group" style="max-height:200px; overflow-y:auto; background:white; border:1px solid #ddd; border-radius:4px;">';
                            response.users.forEach(function(user) {
                                html += '<a href="javascript:void(0);" class="list-group-item list-group-item-action mention-item-popup" data-username="' + user.username + '" data-user-id="' + user.id + '">' +
                                    '<div class="d-flex align-items-center">' +
                                    '<img src="assets/images/profile/' + user.profile_pic + '" class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover">' +
                                    '<div><strong>' + user.first_name + ' ' + user.last_name + '</strong><br><small>@' + user.username + '</small></div>' +
                                    '</div></a>';
                            });
                            html += '</div>';
                            $('#popup-mention-autocomplete').html(html).show();
                        } else {
                            $('#popup-mention-autocomplete').hide();
                        }
                    }
                });
            }, 300);
        } else {
            $('#popup-mention-autocomplete').hide();
        }
    } else {
        $('#popup-mention-autocomplete').hide();
    }
});

// Chọn mention trong modal
$(document).on('click', '.mention-item-popup', function() {
    var $input = $('#popup_comment_input');
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
    
    $('#popup-mention-autocomplete').hide();
});
$(document).on('click', '.followbtn', function(){
    var user_id = $(this).data('userId'); // Lấy ID người cần follow
    var button = $(this); // Lưu nút đang bấm vào biến

    $.ajax({
        url: 'assets/php/ajax.php',
        method: 'post',
        dataType: 'json',
        data: { follow_user: true, user_id: user_id },
        success: function(response){
            if(response.status){
                button.attr('disabled', false); // Bật lại nút
                if(button.hasClass('text-primary')){
                    button.removeClass('text-primary followbtn').addClass('text-muted fw-normal');
                    button.text('Đang theo dõi');
                    button.attr('disabled', true); // Sidebar gợi ý thì follow xong khóa nút luôn cho đẹp
                } 
                else {
                    button.text('Bỏ theo dõi');
                    button.removeClass('btn-outline-primary followbtn').addClass('btn-danger unfollowbtn');
                }
            }
        }
    });
});
$(document).on('click', '.unfollowbtn', function(){
    var user_id = $(this).data('userId');
    var button = $(this);

    $.ajax({
        url: 'assets/php/ajax.php',
        method: 'post',
        dataType: 'json',
        data: { unfollow_user: true, user_id: user_id },
        success: function(response){
            if(response.status){
                
                button.text('Theo dõi');
                button.removeClass('btn-danger unfollowbtn').addClass('btn-outline-primary followbtn');
            }
        }
    });
});
$('#search_user').on('keyup', function(){
    var keyword = $(this).val(); // Lấy chữ đang gõ
    if(keyword.length > 0){
        $.ajax({
            url: 'assets/php/ajax.php?search_mode', // Gọi đến file xử lý
            method: 'post',
            dataType: 'json',
            data: {keyword: keyword},
            success: function(response){
                if(response.status){
                    $('#search_result').html(response.html);
                    $('#search_result').show();
                } else {
                    $('#search_result').html('<p class="text-center text-muted p-2 m-0">User not found</p>');
                    $('#search_result').show();
                }
            }
        });
    } else {
        $('#search_result').hide();
    }
});
$(document).mouseup(function(e){
    var container = $("#search_result");
    var input = $("#search_user");
    if (!container.is(e.target) && container.has(e.target).length === 0 && !input.is(e.target)) {
        container.hide();
    }
});

$(document).on('click', '.delete_post_btn', function(e) {
    e.preventDefault();

    var post_id = $(this).data('post-id');

    if(confirm('Bạn có chắc chắn muốn xóa bài viết này?')){
        $.ajax({
            url: 'assets/php/ajax.php?delete_post',
            method: 'POST',
            dataType: 'json',
            data: { post_id: post_id },
            success: function(response){
                if(response.status){
                    $('.card[data-post-id="' + post_id + '"]').fadeOut(300, function(){
                        $(this).remove();
                    });
                } else {
                    alert('Lỗi: ' + response.msg);
                }
            }
        });
    }
});
$(document).on('click', '.edit_post_btn', function() {
    var post_id = $(this).data('post-id');
    var post_text = $(this).data('post-text');
    var post_img = $(this).data('post-img');

    $('#edit_post_id').val(post_id);
    $('#edit_post_text').val(post_text);
    if(post_img){
        $('#edit_post_img_preview').attr('src', 'assets/images/posts/'+post_img).show();
    }else{
        $('#edit_post_img_preview').hide();
    }
});
$('#save_edit_btn').click(function(){
    var post_id = $('#edit_post_id').val();
    var post_text = $('#edit_post_text').val();
    $(this).text('Đang lưu...').attr('disabled', true);

    $.ajax({
        url: 'assets/php/ajax.php?edit_post', // Đường dẫn file xử lý PHP
        method: 'POST',
        dataType: 'json',
        data: {
            post_id: post_id,
            post_text: post_text
        },
        success: function(response){
            if(response.status){
                location.reload(); // Load lại trang để thấy nội dung mới
            }else{
                alert(response.msg);
                $('#save_edit_btn').text('Lưu thay đổi').attr('disabled', false);
            }
        }
    });
});

$(document).on('click', '.copy_link_btn', function(e) {
    e.preventDefault();
    var url = $(this).data('post-url');
    

    navigator.clipboard.writeText(url).then(function() {

        alert('Đã sao chép liên kết vào bộ nhớ tạm!'); 
    }, function(err) {
        console.error('Lỗi khi sao chép: ', err);
    });
});

$('.bi-bell').parent().click(function(){
    $.ajax({
        url: 'assets/php/ajax.php?read_notification',
        method: 'POST',
        success: function(){
            $('.badge.bg-danger').remove();
        }
    });
});