<?php

if (isset($_GET['search'])) {
    $keyword = $_GET['search'];
    $data = searchUser($keyword); // Gọi hàm tìm kiếm
}
?>

<div class="container mt-4" style="max-width: 800px;">
    <h4 class="mb-4">Kết quả tìm kiếm cho: "<strong><?= $keyword ?></strong>"</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (count($data) < 1): ?>
                <div class="text-center p-4">
                    <i class="bi bi-search display-4 text-muted"></i>
                    <p class="mt-3 text-muted">Không tìm thấy người dùng nào.</p>
                </div>
            <?php else: ?>

                <?php foreach ($data as $f_user):
                    // Bỏ qua chính mình
                    if ($f_user['id'] == $_SESSION['userdata']['id']) continue;
                ?>
                    <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <a href="?u=<?= $f_user['username'] ?>">
                                <img src="assets/images/profile/<?= $f_user['profile_pic'] ?>"
                                    class="rounded-circle border"
                                    style="width:50px; height:50px; object-fit:cover">
                            </a>
                            <div class="ms-3">
                                <a href="?u=<?= $f_user['username'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="m-0 fw-bold"><?= $f_user['first_name'] ?> <?= $f_user['last_name'] ?></h6>
                                </a>
                                <small class="text-muted">@<?= $f_user['username'] ?></small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <?php if (checkFollowStatus($f_user['id'])): ?>
                                <button class="btn btn-sm btn-danger unfollowbtn" data-user-id='<?= $f_user['id'] ?>'>
                                    Unfollow
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary followbtn" data-user-id='<?= $f_user['id'] ?>'>
                                    Follow
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>
</div>