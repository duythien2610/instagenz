var input_post = document.querySelector("#select_post_img");
if (input_post) {
    input_post.addEventListener("change", function() {
        previewImage(this, "#post_img_preview");
    });
}
var input_profile = document.querySelector("#formFile");
if (input_profile) {
    input_profile.addEventListener("change", function() {
        previewImage(this, "#profile_img_pre");
    });
}

function previewImage(input, img_selector) {
    var fileObject = input.files[0];
    var fileReader = new FileReader();

    fileReader.readAsDataURL(fileObject);

    fileReader.onload = function() {
        var result = fileReader.result;
        var img = document.querySelector(img_selector);

        img.setAttribute("src", result);
        img.style.display = "block";
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
            }
        }
    });
});
$('#popup_add_comment_btn').click(function(){
    var post_id = $(this).data('postId');
    var comment_text = $('#popup_comment_input').val();
    var button = this;

    if(comment_text == '') return;

    $(button).attr('disabled', true);

    $.ajax({
        url: 'assets/php/ajax.php?addcomment',
        method: 'post',
        dataType: 'json',
        data: {post_id: post_id, comment: comment_text},
        success: function(response){
            if(response.status){
                $('#popup_comment_list').append(response.comment);
                $('#popup_comment_input').val('');
                $(button).attr('disabled', false);
                var commentList = document.getElementById('popup_comment_list');
                commentList.scrollTop = commentList.scrollHeight;
            }else{
                alert('Lỗi!');
                $(button).attr('disabled', false);
            }
        }
    });
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
                    button.text('Following');
                    button.attr('disabled', true); // Sidebar gợi ý thì follow xong khóa nút luôn cho đẹp
                } 
                else {
                    button.text('Unfollow');
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
                
                button.text('Follow');
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