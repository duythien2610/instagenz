<?php
// pages/sidebar.php

// 1. Lấy thông tin người dùng đang đăng nhập
$user = $_SESSION['userdata'];

// 2. Lấy danh sách GỢI Ý (Những người mình chưa follow)
$suggestions = filterFollowSuggestions();
?>

<div class="card shadow-sm">
    <div class="card-body d-flex flex-column align-items-center">
        <a href="?u=<?= $user['username'] ?>" class="text-decoration-none">
            <img src="assets/images/profile/<?= $user['profile_pic'] ?>"
                class="rounded-circle border"
                style="width: 80px; height: 80px; object-fit: cover;">
        </a>

        <a href="?u=<?= $user['username'] ?>" class="text-decoration-none text-dark mt-2">
            <h5 class="fw-bold m-0"><?= $user['first_name'] ?> <?= $user['last_name'] ?></h5>
        </a>
        <p class="text-muted">@<?= $user['username'] ?></p>

        <a href="?editprofile" class="btn btn-sm btn-outline-dark w-100">
            Edit Profile
        </a>
    </div>
</div>

<div class="card shadow-sm mt-3 mb-4">
    <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-muted m-0">Suggestions for you</h6>
        </div>
    </div>

    <div class="card-body">
        <?php if (count($suggestions) < 1): ?>
            <p class="text-muted small text-center">Bạn đã theo dõi tất cả mọi người!</p>
        <?php else: ?>

            <div class="d-flex flex-column gap-2">

                <?php foreach ($suggestions as $s_user): ?>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="?u=<?= $s_user['username'] ?>">
                                <img src="assets/images/profile/<?= $s_user['profile_pic'] ?>"
                                    class="rounded-circle border"
                                    style="width:35px; height:35px; object-fit:cover">
                            </a>

                            <div class="ms-2">
                                <a href="?u=<?= $s_user['username'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="m-0 small fw-bold text-truncate" style="max-width: 100px;">
                                        <?= $s_user['first_name'] ?> <?= $s_user['last_name'] ?>
                                    </h6>
                                </a>
                                <small class="text-muted d-block" style="font-size:11px">@<?= $s_user['username'] ?></small>
                            </div>
                        </div>

                        <button class="btn btn-sm text-primary fw-bold py-0 px-2 followbtn border-0"
                            data-user-id="<?= $s_user['id'] ?>"
                            style="font-size: 12px;">
                            Follow
                        </button>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
    </div>
</div>