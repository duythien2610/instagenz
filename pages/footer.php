<div class="modal fade" id="addpost" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Preview Image Area -->
                <div id="post_img_preview_container" style="display:none; margin-bottom: 20px;">
                    <div class="position-relative" style="max-height: 500px; overflow: hidden; border-radius: 8px; border: 1px solid #dee2e6;">
                        <img src="" id="post_img_preview" class="w-100" style="object-fit: contain; max-height: 500px; display: block;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="remove_preview_btn" style="z-index: 10;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Gửi form tới actions.php gốc với tham số đúng là add_post -->
                <form method="post" action="actions.php?add_post" enctype="multipart/form-data" id="add_post_form">
                    <div class="my-3">
                        <label for="select_post_img" class="form-label">Chọn hình ảnh</label>
                        <input class="form-control" type="file" name="post_img" id="select_post_img" accept="image/*">
                        <small class="text-muted">Chọn file hình ảnh để đăng bài</small>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Say Something</label>
                        <textarea name="post_text" class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Say something..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="likesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Likes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="likesModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="postViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-8 d-flex align-items-center bg-black justify-content-center">
                        <img src="" id="popup_post_img" style="max-height: 80vh; max-width: 100%; object-fit: contain;">
                    </div>

                    <div class="col-md-4 d-flex flex-column" style="height: 80vh;">

                        <div class="d-flex align-items-center p-3 border-bottom">
                            <img src="" id="popup_user_img" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                            <div class="ms-2">
                                <h6 class="m-0 fw-bold" id="popup_user_name"></h6>
                                <small class="text-muted" style="font-size: 12px;">
                                    <span id="popup_username"></span>
                                    <span class="mx-1">•</span>
                                    <span id="popup_posted_time"></span>
                                </small>
                            </div>

                            <div class="dropdown ms-auto">
                                <a href="#" class="text-dark" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots" style="cursor:pointer; font-size: 1.5rem;"></i>
                                </a>
                                <ul class="dropdown-menu" id="popup_post_menu">
                                </ul>
                            </div>
                        </div>

                        <div class="flex-grow-1 p-3" style="overflow-y: auto;" id="popup_comment_list_container">
                            <p id="popup_caption" class="mb-3"></p>
                            <div id="popup_comment_list" style="min-height: 100px;">
                            </div>
                        </div>

                        <div class="p-3 border-top bg-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-heart-fill text-danger unlike_btn" id="popup_unlike_btn" style="display:none; cursor:pointer; font-size: 1.5rem;"></i>
                                <i class="bi bi-heart like_btn" id="popup_like_btn" style="display:none; cursor:pointer; font-size: 1.5rem;"></i>
                                <span class="ms-2 fw-bold" id="popup_like_count"></span>
                            </div>
                        </div>

                        <div class="p-3 border-top bg-white position-relative">
                            <div class="input-group">
                                <input type="text" class="form-control comment-input" id="popup_comment_input" placeholder="Add a comment..." data-parent-id="0">
                                <button class="btn btn-outline-primary add-comment-btn" id="popup_add_comment_btn" type="button" data-parent-id="0">Post</button>
                            </div>
                            <div class="mention-autocomplete" id="popup-mention-autocomplete" style="display:none; position:absolute; bottom:100%; left:0; right:0; z-index:1000;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_post_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_post_form">
                    <input type="hidden" name="post_id" id="edit_post_id">

                    <div class="mb-3 text-center">
                        <img id="edit_post_img_preview" src="" style="max-width: 100%; max-height: 300px; display:none; border-radius:10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-control" name="post_text" id="edit_post_text" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="save_edit_btn">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>


<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.1.js"></script>
<script src="assets/js/custom.js?v=<?= time() ?>"></script>
<script>
// Xử lý parameter post_view từ URL để tự động mở modal
$(document).ready(function() {
    // Lấy parameter post_view từ URL
    var urlParams = new URLSearchParams(window.location.search);
    var postViewId = urlParams.get('post_view');
    
    if (postViewId) {
        // Tìm element với class show_post_modal và data-post-id tương ứng
        var $postElement = $('.show_post_modal[data-post-id="' + postViewId + '"]');
        
        if ($postElement.length > 0) {
            // Trigger click vào element để mở modal
            setTimeout(function() {
                $postElement.click();
                // Xóa parameter khỏi URL để không bị reload lại
                var newUrl = window.location.pathname + window.location.search.replace(/[?&]post_view=\d+/, '');
                if (newUrl.endsWith('?')) newUrl = newUrl.slice(0, -1);
                window.history.replaceState({}, document.title, newUrl);
            }, 500); // Delay một chút để đảm bảo modal đã được load
        } else {
            // Nếu không tìm thấy element trên trang, vẫn mở modal bằng cách gọi trực tiếp
            setTimeout(function() {
                var post_id = parseInt(postViewId);
                $('#popup_add_comment_btn').data('post-id', post_id);
                $('#popup_like_btn').data('post-id', post_id);
                $('#popup_unlike_btn').data('post-id', post_id);
                $('#postViewModal').modal('show');
                $('#popup_post_menu').html(''); 
                $('#popup_post_img').attr('src', '');
                
                $.ajax({
                    url: 'assets/php/ajax.php?get_post_view',
                    method: 'POST',
                    dataType: 'json',
                    data: {post_id: post_id},
                    success: function(response) {
                        if(response.status) {
                            $('#popup_post_img').attr('src', 'assets/images/posts/' + response.post_img);
                            $('#popup_user_img').attr('src', 'assets/images/profile/' + response.profile_pic);
                            $('#popup_user_name').text(response.fullname);
                            $('#popup_username').text('@' + response.username);
                            $('#popup_caption').text(response.post_text);
                            $('#popup_posted_time').text(response.created_at);
                            $('#popup_comment_list').html(response.comments_html);
                            if(response.like_count > 0) {
                                $('#popup_like_count').text(response.like_count + ' likes');
                            } else {
                                $('#popup_like_count').text('Be the first to like this');
                            }
                            if(response.is_liked) {
                                $('#popup_unlike_btn').show();
                                $('#popup_like_btn').hide();
                            } else {
                                $('#popup_unlike_btn').hide();
                                $('#popup_like_btn').show();
                            }
                            var menu_html = '';
                            if(response.is_own_post) {
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
                                var postUrl = window.location.origin + window.location.pathname + '?post_view=' + response.id;
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
                // Xóa parameter khỏi URL
                var newUrl = window.location.pathname + window.location.search.replace(/[?&]post_view=\d+/, '');
                if (newUrl.endsWith('?')) newUrl = newUrl.slice(0, -1);
                window.history.replaceState({}, document.title, newUrl);
            }, 500);
        }
    }
});
</script>
</body>

</html>