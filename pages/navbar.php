<?php
// Kiểm tra và lấy thông tin user từ Session
if (isset($_SESSION['userdata'])) {
    $user = $_SESSION['userdata'];
} else {
    // Nếu không có dữ liệu user thì ẩn bớt hoặc gán mảng rỗng để tránh lỗi
    $user = array('profile_pic' => 'default_profile.jpg');
}

$unread_count = countUnreadNotifications($_SESSION['userdata']['id']);

$notifications = getNotifications($_SESSION['userdata']['id']);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border">
    <div class="container col-9 d-flex justify-content-between">
        <div class="d-flex justify-content-between col-8">
            <a class="navbar-brand" href="?">
                <img src="assets/images/instagenz.png" alt="" height="50">

            </a>

            <form class="d-flex position-relative" method="GET" action="index.php">

                <input class="form-control me-2" type="search" name="search" id="search_user"
                    placeholder="Looking for someone.." aria-label="Search" autocomplete="off">

                <button class="btn btn-outline-dark" type="submit"><i class="bi bi-search"></i></button>

                <div class="bg-white text-end border rounded shadow-sm position-absolute" id="search_result"
                    style="top: 100%; left: 0; width: 100%; z-index: 999; display: none; max-height: 300px; overflow-y: auto;">
                </div>

            </form>

        </div>


        <ul class="navbar-nav  mb-2 mb-lg-0">

            <li class="nav-item">
                <a class="nav-link text-dark" href="?"><i class="bi bi-house-door-fill"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" data-bs-toggle="modal" data-bs-target="#addpost" href="#"><i class="bi bi-plus-square-fill"></i></a>
            </li>
            <div class="dropdown">
                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill"></i>

                    <?php if ($unread_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?= $unread_count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="p-2 border-bottom fw-bold">Thông báo</li>

                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <?php
                            // Nếu chưa đọc thì background màu nhạt, đọc rồi thì màu trắng
                            $bg_class = ($notif['read_status'] == 0) ? 'bg-light' : 'bg-white';

                            // Link: Nếu có post_id thì bấm vào xem bài, không thì xem trang cá nhân
                            $link = ($notif['post_id'] > 0) ? "?post_view=" . $notif['post_id'] : "?u=" . $notif['username'];
                            ?>

                            <li>
                                <a class="dropdown-item d-flex align-items-center p-2 <?= $bg_class ?>" href="<?= $link ?>">
                                    <img src="assets/images/profile/<?= $notif['profile_pic'] ?>" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                                    <div class="ms-2 text-wrap">
                                        <span class="fw-bold"><?= $notif['first_name'] . ' ' . $notif['last_name'] ?></span>
                                        <?= $notif['message'] ?>
                                        <br>
                                        <small class="text-muted"><?= show_time_ago($notif['created_at']) ?></small>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="p-3 text-center text-muted">Không có thông báo nào.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <li class="nav-item">
                <a class="nav-link text-dark" href="#"><i class="bi bi-chat-right-dots-fill"></i></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="assets/images/profile/<?= $user['profile_pic'] ?>" alt="" height="30" class="rounded-circle border">
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item" href="?u=<?= $_SESSION['userdata']['username'] ?>">
                            <i class="bi bi-person-circle"></i> My Profile
                        </a>
                    </li>
                    <li><a class="dropdown-item" href="?editprofile"><i class="bi bi-pencil"></i> Edit Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Account Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="actions.php?logout"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                </ul>
            </li>

        </ul>


    </div>
</nav>