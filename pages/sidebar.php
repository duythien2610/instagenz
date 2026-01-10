<?php
// pages/sidebar.php

// 1. Kiểm tra session an toàn
$user = isset($_SESSION['userdata']) ? $_SESSION['userdata'] : null;

// Nếu chưa đăng nhập thì ẩn sidebar
if (!$user) return; 

// 2. Xử lý ảnh đại diện của User 
$default_img = 'assets/images/profile/default_profile.jpg';
$my_profile_pic = $default_img;

if (!empty($user['profile_pic'])) {
    $target_file = 'assets/images/profile/' . $user['profile_pic'];
    // Kiểm tra file có thật trên ổ cứng không
    if (file_exists($target_file)) {
        $my_profile_pic = $target_file;
    }
}

// 3. Lấy gợi ý follow (Check hàm tồn tại)
$suggestions = [];
if (function_exists('filterFollowSuggestions')) {
    $suggestions = filterFollowSuggestions();
}
?>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body d-flex flex-column align-items-center py-4">
        <a href="?u=<?= $user['username'] ?>" class="text-decoration-none">
            <img src="<?= $my_profile_pic ?>" 
                 class="rounded-circle border border-2" 
                 width="80" height="80" 
                 style="object-fit: cover; padding: 2px;" 
                 alt="Avatar">
        </a>

        <a href="?u=<?= $user['username'] ?>" class="text-decoration-none text-dark mt-3">
            <h6 class="fw-bold m-0"><?= $user['first_name'] . ' ' . $user['last_name'] ?></h6>
        </a>
        <p class="text-muted small mb-3">@<?= $user['username'] ?></p>

        <a href="?editprofile" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-bold">
            Edit Profile
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 mt-3">
    <div class="card-header bg-white border-0 pb-0 pt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-secondary m-0" style="font-size: 0.9rem;">Suggestions for you</h6>
            <a href="#" class="text-decoration-none fw-bold text-dark" style="font-size: 0.8rem;">See All</a>
        </div>
    </div>

    <div class="card-body">
        <?php if (count($suggestions) < 1): ?>
            <p class="text-muted small text-center my-3">No suggestions available.</p>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($suggestions as $s_user): ?>
                    <?php
                    // Xử lý ảnh người lạ
                    $s_pic = $default_img;
                    if (!empty($s_user['profile_pic'])) {
                        $path = 'assets/images/profile/' . $s_user['profile_pic'];
                        if (file_exists($path)) {
                            $s_pic = $path;
                        }
                    }
                    ?>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="?u=<?= $s_user['username'] ?>">
                                <img src="<?= $s_pic ?>" 
                                     class="rounded-circle border" 
                                     width="35" height="35" 
                                     style="object-fit: cover;">
                            </a>

                            <div class="ms-2">
                                <a href="?u=<?= $s_user['username'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="m-0 small fw-bold text-truncate" style="max-width: 100px;">
                                        <?= $s_user['username'] ?>
                                    </h6>
                                </a>
                                <small class="text-muted d-block" style="font-size: 11px;">Suggested for you</small>
                            </div>
                        </div>

                        <button class="btn btn-sm text-primary fw-bold py-0 px-2 follow-btn border-0 bg-transparent"
                            data-user-id="<?= $s_user['id'] ?>"
                            style="font-size: 12px;">
                            Follow
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-footer bg-transparent border-0">
        <p class="text-muted text-center" style="font-size: 10px;">
            © 2024 INSTAGENZ FROM HUB 
        </p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Sử dụng off().on() để tránh gán sự kiện nhiều lần nếu file này được include lại
    $(document).off('click', '.follow-btn').on('click', '.follow-btn', function() {
        var user_id = $(this).data('user-id');
        var btn = $(this);

        // Hiệu ứng UX: Đổi ngay sang trạng thái "đang xử lý" hoặc "Following"
        btn.prop('disabled', true); // Khóa nút để không bấm liên tục

        $.ajax({
            url: 'assets/php/ajax.php?follow=1',
            method: 'POST',
            data: { user_id: user_id },
            dataType: 'json',
            success: function(resp){
                if(resp.status){
                    // Đổi giao diện nút thành "Following" (màu xám, chữ đen)
                    btn.text('Following');
                    btn.removeClass('text-primary follow-btn'); // Bỏ màu xanh
                    btn.addClass('text-muted text-decoration-none'); // Thêm màu xám
                    btn.css('cursor', 'default');
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại');
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('Lỗi kết nối server');
                btn.prop('disabled', false);
            }
        });
    });
});
</script>