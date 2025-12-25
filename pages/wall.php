<?php
// 1. Lấy thông tin user từ Session (CHUẨN)
if (isset($_SESSION['userdata'])) {
    $user = $_SESSION['userdata'];
} else {
    header('location: index.php?login');
}

// 2. Lấy danh sách bài viết trực tiếp (Tránh lỗi null)
$posts = filterPosts();;
?>
<div class="container col-9 rounded-0 d-flex justify-content-between">
    <div class="col-4 mt-4 p-3">
        <?php require_once 'pages/sidebar.php'; ?>
    </div>
    <!-- <div class="col-4 mt-4 p-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex flex-column align-items-center">
                    <img src="assets/images/profile/<?= $user['profile_pic'] ?>" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                    <h5 class="mt-3"><?= $user['first_name'] . ' ' . $user['last_name'] ?></h5>
                    <p class="text-muted">@<?= $user['username'] ?></p>
                    <a href="?editprofile" class="btn btn-sm btn-outline-primary w-100">Edit Profile</a>
                </div>
            </div>
        </div> -->

    <div class="col-7 mt-4">

        <!-- <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addpost" style="cursor: pointer;">
                        <img src="assets/images/profile/<?= $user['profile_pic'] ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                        <input type="text" class="form-control rounded-pill border-0 bg-light" placeholder="What's on your mind, <?= $user['first_name'] ?>?" readonly>
                    </div>
                </div>
            </div> -->

        <?php
        if (count($posts) < 1) {
        ?>
            <div class="card shadow-sm text-center p-5">
                <div class="card-body">
                    <i class="bi bi-newspaper display-4 text-muted"></i>
                    <h5 class="mt-3">Bảng tin đang trống!</h5>
                    <p class="text-muted">Hãy Follow mọi người ở danh sách gợi ý bên trái để thấy bài viết của họ tại đây.</p>
                </div>
            </div>
        <?php
        }
        foreach ($posts as $post) {
        ?>
            <div class="card mt-4 shadow-sm" data-post-id="<?= $post['id'] ?>">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="?u=<?= $post['username'] ?>" class="text-decoration-none text-dark">
                            <img src="assets/images/profile/<?= $post['profile_pic'] ?>" alt=""
                                data-post-id="<?= $post['id'] ?>" height="40" width="40" class="rounded-circle border" style="object-fit:cover;"></a>
                        &nbsp;&nbsp;
                        <div>
                            <a href="?u=<?= $post['username'] ?>" class="text-decoration-none text-dark">
                                <h6 class="mb-0"><?= $post['first_name'] ?> <?= $post['last_name'] ?></h6>
                            </a>

                            <small class="text-muted" style="font-size: 12px;">
                                @<?= $post['username'] ?>
                                <span class="mx-1">•</span>
                                <?= show_time_ago($post['created_at']) ?>
                            </small>
                        </div>
                    </div>
                    <div class="p-2">
                        <div class="dropdown">
                            <span class="" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical" style="cursor:pointer"></i>
                            </span>

                            <ul class="dropdown-menu">
                                <?php if ($post['user_id'] == $user['id']): ?>
                                    <li>
                                        <a class="dropdown-item edit_post_btn" href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit_post_modal"
                                            data-post-id="<?= $post['id'] ?>"
                                            data-post-text="<?= htmlspecialchars($post['post_text']) ?>"
                                            data-post-img="<?= $post['post_img'] ?>">
                                            <i class="bi bi-pencil-square"></i> Sửa bài viết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete_post_btn" href="#" data-post-id="<?= $post['id'] ?>">
                                            <i class="bi bi-trash"></i> Xóa bài viết
                                        </a>
                                    </li>

                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item copy_link_btn" href="#"
                                            data-post-url="<?= 'http://localhost/instagenz/?u=' . $post['username'] . '&post_view=' . $post['id'] ?>">
                                            <i class="bi bi-clipboard"></i> Sao chép liên kết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-flag"></i> Báo cáo
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if ($post['post_img']): ?>
                    <img src="assets/images/posts/<?= $post['post_img'] ?>" class="card-img-top show_post_modal" data-post-id="<?= $post['id'] ?>" style="cursor:pointer" alt="...">
                <?php endif; ?>

                <div class="card-body">
                    <?php if ($post['post_text']): ?>
                        <p class="card-text"><?= $post['post_text'] ?></p>
                    <?php endif; ?>

                    <?php
                    $likes = getLikes($post['id']);
                    $is_liked = checkLikeStatus($post['id']);
                    $comments = getComments($post['id']);
                    ?>
                    <div class="d-flex align-items-center mt-3 border-top pt-2">
                        <span class="me-3">
                            <i class="bi bi-heart-fill text-danger unlike_btn"
                                style="cursor:pointer; font-size:1.2rem;display:<?= $is_liked ? 'inline-block' : 'none' ?>"
                                data-post-id="<?= $post['id'] ?>"></i>
                            <i class="bi bi-heart like_btn"
                                style="cursor:pointer; font-size:1.2rem; display: <?= $is_liked ? 'none' : 'inline-block' ?>"
                                data-post-id="<?= $post['id'] ?>"></i>
                            <span class="ms-1 fw-bold show_likes" style="cursor:pointer" data-post-id="<?= $post['id'] ?>"><?= count($likes) ?></span>
                        </span>
                        <span class="ms-3 text-muted show_post_modal" style="cursor:pointer" data-post-id="<?= $post['id'] ?>">
                            <i class="bi bi-chat-left"></i> <?= count($comments) ?> comment
                        </span>
                    </div>
                    <div class="collapse mt-2" id="comments-<?= $post['id'] ?>">

                        <div class="comment-list" id="comment-list-<?= $post['id'] ?>">
                            <?php foreach ($comments as $comment):
                                $cuser = getUser($comment['user_id']); // Lấy thông tin người comment
                            ?>
                                <div class="d-flex align-items-center p-2">
                                    <div><img src="assets/images/profile/<?= $cuser['profile_pic'] ?>" class="rounded-circle border" height="40"></div>
                                    <div class="ms-2">
                                        <h6 class="m-0 fw-bold"><?= $cuser['first_name'] ?> <?= $cuser['last_name'] ?></h6>
                                        <p class="m-0 text-muted"><?= $comment['comment'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex mt-2">
                            <input type="text" class="form-control rounded-0 border-0 comment-input" placeholder="Viết bình luận..." data-post-id="<?= $post['id'] ?>">
                            <button class="btn btn-outline-primary rounded-0 border-0 add-comment-btn" data-post-id="<?= $post['id'] ?>">Gửi</button>
                        </div>
                    </div>

                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<div class="modal fade" id="addpost" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <img src="" id="post_img_preview" style="display:none; width: 100%; border-radius: 10px; margin-bottom: 15px;" class="shadow-sm">

                <form method="post" action="actions.php?add_post" enctype="multipart/form-data">
                    <div class="my-3">
                        <input class="form-control" name="post_img" type="file" id="select_post_img" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="post_text" rows="3" placeholder="Say something..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>