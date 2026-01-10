<?php
// 1. XỬ LÝ DỮ LIỆU USER & POST
if (isset($_GET['u'])) {
    $u = $_GET['u'];
    $profile = getUserByUsername($u);
} else {
    $profile = $_SESSION['userdata'];
}

// Lấy danh sách bài viết
$profile_post = getPostById($profile['id']);

// Lấy danh sách Followers (Người theo dõi user này)
$profile_followers = getFollowers($profile['id']);

// Lấy danh sách Following (User này đang theo dõi ai)
$profile_following = getFollowing($profile['id']);

$user = $_SESSION['userdata']; // Người đang đăng nhập (là mình)
?>

<div class="container col-9 rounded-0">

    <div class="col-12 rounded p-4 mt-4 d-flex gap-5">
        <div class="col-4 d-flex justify-content-end align-items-start">
            <img src="assets/images/profile/<?= $profile['profile_pic'] ?>"
                class="img-thumbnail rounded-circle my-3" style="height:170px; width:170px; object-fit:cover" alt="...">
        </div>

        <div class="col-8">
            <div class="d-flex flex-column">
                <div class="d-flex gap-5 align-items-center">
                    <span style="font-size: xx-large;"><?= $profile['first_name'] . ' ' . $profile['last_name'] ?></span>

                    <?php if ($user['id'] != $profile['id']): ?>
                        <div class="dropdown">
                            <span class="" style="font-size:xx-large" type="button" id="dropdownMenuButton1"
                                data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i> </span>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-chat-fill"></i> Message</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-x-circle-fill"></i> Block</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <span style="font-size: larger;" class="text-secondary">@<?= $profile['username'] ?></span>

                <div class="d-flex gap-2 align-items-center my-3">
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-file-post-fill"></i> <?= count($profile_post) ?> bài viết
                    </button>

                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#followersModal">
                        <i class="bi bi-people-fill"></i> <span id="follower_count"><?= count($profile_followers) ?></span> người theo dõi
                    </button>

                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#followingModal">
                        <i class="bi bi-person-fill"></i> <?= count($profile_following) ?> đang theo dõi
                    </button>
                </div>

                <?php if ($user['id'] != $profile['id']): ?>
                    <div class="d-flex gap-2 align-items-center my-1">
                        <?php if (checkFollowStatus($profile['id'])): ?>
                            <button class="btn btn-sm btn-secondary unfollowbtn" data-user-id='<?= $profile['id'] ?>'>Đang theo dõi</button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-primary followbtn" data-user-id='<?= $profile['id'] ?>'>Theo dõi</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3 class="border-bottom mt-4">Posts</h3>
    <?php if (count($profile_post) < 1): ?>
        <p class='p-2 bg-white border rounded text-center'>User has no posts.</p>
    <?php endif; ?>

    <div class="row g-2 mb-4">
        <?php foreach ($profile_post as $post): ?>
            <div class="col-4">
                <div class="card border-0 ratio ratio-1x1">
                    <img src="assets/images/posts/<?= $post['post_img'] ?>"
                        class="card-img-top rounded object-fit-cover show_post_modal"
                        data-post-id="<?= $post['id'] ?>"
                        style="cursor:pointer; width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<div class="modal fade" id="followersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Người theo dõi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php foreach ($profile_followers as $f):
                    $f_user = getUser($f['follower_id']); 
                    if (!$f_user) continue;
                    $followed_by_me = checkFollowStatus($f['follower_id']);
                    $is_me = ($user['id'] == $f['follower_id']); 
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/profile/<?= $f_user['profile_pic'] ?>" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                            <div class="ms-2">
                                <a href="?u=<?= $f_user['username'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="m-0 fw-bold"><?= $f_user['first_name'] . ' ' . $f_user['last_name'] ?></h6>
                                </a>
                                <small class="text-muted">@<?= $f_user['username'] ?></small>
                            </div>
                        </div>

                        <div>
                            <?php if (!$is_me): ?>
                                <?php if ($followed_by_me): ?>
                                    <button class="btn btn-sm btn-secondary unfollowbtn" data-user-id="<?= $f_user['id'] ?>">Đang theo dõi</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary followbtn" data-user-id="<?= $f_user['id'] ?>">Theo dõi</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (count($profile_followers) < 1) echo "<p class='text-center text-muted'>Chưa có người theo dõi.</p>"; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="followingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đang theo dõi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php foreach ($profile_following as $f):
                    $f_user = getUser($f['user_id']); 
                    if (!$f_user) continue;
                    $followed_by_me = checkFollowStatus($f['user_id']);
                    $is_me = ($user['id'] == $f['user_id']);
                ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/profile/<?= $f_user['profile_pic'] ?>" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                            <div class="ms-2">
                                <a href="?u=<?= $f_user['username'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="m-0 fw-bold"><?= $f_user['first_name'] . ' ' . $f_user['last_name'] ?></h6>
                                </a>
                                <small class="text-muted">@<?= $f_user['username'] ?></small>
                            </div>
                        </div>

                        <div>
                            <?php if (!$is_me): ?>
                                <?php if ($followed_by_me): ?>
                                    <button class="btn btn-sm btn-secondary unfollowbtn" data-user-id="<?= $f_user['id'] ?>">Đang theo dõi</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary followbtn" data-user-id="<?= $f_user['id'] ?>">Theo dõi</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (count($profile_following) < 1) echo "<p class='text-center text-muted'>Chưa theo dõi ai.</p>"; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    // --- 1. XỬ LÝ NÚT FOLLOW (Optimistic UI - Cập nhật ngay lập tức) ---
    $(document).on('click', '.followbtn', function() {
        var user_id = $(this).data('user-id');
        var btn = $(this);

        // Đổi giao diện NGAY LẬP TỨC (Giống Instagram)
        // Từ nút Xanh (Follow) -> Nút Xám (Following)
        btn.removeClass('followbtn btn-primary')
           .addClass('unfollowbtn btn-secondary')
           .text('Following');

        // Gửi Ajax ngầm
        $.ajax({
            url: 'assets/php/ajax.php?follow=1',
            method: 'POST',
            data: { user_id: user_id },
            success: function(resp){
                // Nếu đây là trang profile của người mình vừa follow -> Tăng số follow lên 1
                // Chỉ áp dụng nếu nút bấm nằm ở phần header (không phải trong modal)
                if(btn.closest('.col-8').length > 0){ 
                   var count = $('#follower_count');
                   count.text(parseInt(count.text()) + 1);
                }
            }
        });
    });

    // --- 2. XỬ LÝ NÚT UNFOLLOW ---
    $(document).on('click', '.unfollowbtn', function() {
        var user_id = $(this).data('user-id');
        var btn = $(this);

        // Đổi giao diện NGAY LẬP TỨC
        // Từ nút Xám (Following) -> Nút Xanh (Follow)
        btn.removeClass('unfollowbtn btn-secondary')
           .addClass('followbtn btn-primary')
           .text('Follow');

        // Gửi Ajax ngầm
        $.ajax({
            url: 'assets/php/ajax.php?unfollow=1',
            method: 'POST',
            data: { user_id: user_id },
            success: function(resp){
                // Nếu đây là trang profile của người mình vừa unfollow -> Giảm số follow đi 1
                if(btn.closest('.col-8').length > 0){
                   var count = $('#follower_count');
                   count.text(parseInt(count.text()) - 1);
                }
            }
        });
    });

});
</script>