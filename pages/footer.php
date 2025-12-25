<div class="modal fade" id="addpost" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" style="display:none" id="post_img" class="w-100 rounded border">
                <form method="post" action="assets/php/actions.php?addpost" enctype="multipart/form-data">
                    <div class="my-3">
                        <input class="form-control" type="file" name="post_img" id="select_post_img">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Say Something</label>
                        <textarea name="post_text" class="form-control" id="exampleFormControlTextarea1" rows="1"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
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

                        <div class="flex-grow-1 p-3" style="overflow-y: auto;">
                            <p id="popup_caption" class="mb-3"></p>
                            <div id="popup_comment_list">
                            </div>
                        </div>

                        <div class="p-3 border-top bg-white">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-heart-fill text-danger unlike_btn" id="popup_unlike_btn" style="display:none; cursor:pointer; font-size: 1.5rem;"></i>
                                <i class="bi bi-heart like_btn" id="popup_like_btn" style="display:none; cursor:pointer; font-size: 1.5rem;"></i>
                                <span class="ms-2 fw-bold" id="popup_like_count"></span>
                            </div>
                        </div>

                        <div class="p-3 border-top bg-white">
                            <div class="input-group">
                                <input type="text" class="form-control" id="popup_comment_input" placeholder="Add a comment...">
                                <button class="btn btn-outline-primary" id="popup_add_comment_btn" type="button">Post</button>
                            </div>
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
</body>

</html>