<?php
// 1. KHỞI TẠO VÀ KIỂM TRA QUYỀN
if (!isset($_SESSION['Auth']) || !isset($_SESSION['userdata'])) {
    echo '<div class="container py-5 text-center text-danger">Bạn chưa đăng nhập.</div>';
    return;
}

$current_user = $_SESSION['userdata'];
global $db; // Giả định $db là đối tượng kết nối Database của bạn

// 2. XỬ LÝ CÁC HÀNH ĐỘNG (ACTIONS)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'delete_user' && $id > 0) {
    $db->query("DELETE FROM users WHERE id = $id");
    header("Location: index.php?admin&tab=users&msg=UserDeleted");
}

if ($action == 'block_user' && $id > 0) {
    $db->query("UPDATE users SET ac_status = 0 WHERE id = $id");
    header("Location: index.php?admin&tab=users&msg=UserBlocked");
}

if ($action == 'activate_user' && $id > 0) {
    $db->query("UPDATE users SET ac_status = 1 WHERE id = $id");
    header("Location: index.php?admin&tab=users&msg=UserActivated");
}

if ($action == 'delete_post' && $id > 0) {
    $db->query("DELETE FROM posts WHERE id = $id");
    header("Location: index.php?admin&tab=posts&msg=PostDeleted");
}

if ($action == 'delete_comment' && $id > 0) {
    $db->query("DELETE FROM comments WHERE id = $id");
    header("Location: index.php?admin&tab=comments&msg=CommentDeleted");
}

// 3. TRUY VẤN DỮ LIỆU THỐNG KÊ
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

$total_users = $db->query("SELECT count(*) as total FROM users")->fetch_assoc()['total'];
$total_posts = $db->query("SELECT count(*) as total FROM posts")->fetch_assoc()['total'];
$total_comments = $db->query("SELECT count(*) as total FROM comments")->fetch_assoc()['total'];

// Lấy danh sách tùy theo Tab
$users = ($tab == 'users' || $tab == 'dashboard') ? $db->query("SELECT * FROM users ORDER BY id DESC") : [];
$posts = ($tab == 'posts') ? $db->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC") : [];
$comments = ($tab == 'comments') ? $db->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.id DESC") : [];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Instagenz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --sb-width: 260px; --primary: #2563eb; --dark-bg: #0f172a; --light-bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); margin: 0; overflow-x: hidden; }
        
        .sidebar { width: var(--sb-width); height: 100vh; background: var(--dark-bg); position: fixed; left: 0; top: 0; z-index: 1000; color: #fff; padding-top: 20px; }
        .sidebar-brand { padding: 0 25px 30px; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; }
        .nav-label { padding: 10px 25px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .nav-link { padding: 12px 25px; color: #94a3b8; display: flex; align-items: center; gap: 12px; font-size: 14px; transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-right: 3px solid #3b82f6; }
        
        .main-content { margin-left: var(--sb-width); padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .stat-card { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .card-table { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .table th { background: #f8fafc; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; padding: 15px 20px; }
        .table td { padding: 15px 20px; vertical-align: middle; font-size: 14px; }
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="index.php?admin" class="sidebar-brand">
        <i class="bi bi-instagram"></i> INSTAGENZ
    </a>
    <div class="nav flex-column">
        <a class="nav-link <?= $tab == 'dashboard' ? 'active' : '' ?>" href="index.php?admin&tab=dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="nav-label">Cài đặt hệ thống</div>
        <a class="nav-link <?= $tab == 'users' ? 'active' : '' ?>" href="index.php?admin&tab=users">
            <i class="bi bi-person"></i> Quản lý tài khoản
        </a>
        <a class="nav-link <?= $tab == 'posts' ? 'active' : '' ?>" href="index.php?admin&tab=posts">
            <i class="bi bi-file-post"></i> Quản lý bài viết
        </a>
        <a class="nav-link <?= $tab == 'comments' ? 'active' : '' ?>" href="index.php?admin&tab=comments">
            <i class="bi bi-chat-text"></i> Quản lý bình luận
        </a>
        <a class="nav-link mt-5 text-danger" href="assets/php/actions.php?logout">
            <i class="bi bi-box-arrow-left"></i> Đăng xuất
        </a>
    </div>
</div>

<div class="main-content">
    <div class="header">
        <h4 class="fw-bold mb-0">
            <?php 
                $titles = ['dashboard'=>'Tổng quan', 'users'=>'Quản lý tài khoản', 'posts'=>'Quản lý bài viết', 'comments'=>'Quản lý bình luận'];
                echo $titles[$tab] ?? 'Admin';
            ?>
        </h4>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="fw-bold small">Admin</div>
                <div class="text-muted" style="font-size: 11px;"><?= $current_user['email'] ?></div>
            </div>
            <img src="assets/images/profile/<?= $current_user['profile_pic'] ?>" width="40" height="40" class="rounded-circle border">
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-people"></i></div>
                <div><div class="text-muted small">Người dùng</div><h4 class="fw-bold mb-0"><?= $total_users ?></h4></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-success-subtle text-success"><i class="bi bi-card-image"></i></div>
                <div><div class="text-muted small">Bài viết</div><h4 class="fw-bold mb-0"><?= $total_posts ?></h4></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-warning-subtle text-warning"><i class="bi bi-chat-dots"></i></div>
                <div><div class="text-muted small">Bình luận</div><h4 class="fw-bold mb-0"><?= $total_comments ?></h4></div>
            </div>
        </div>
    </div>

    <div class="card-table">
        <?php if($tab == 'users' || $tab == 'dashboard'): ?>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Trạng thái</th>
                    <th>Ngày tham gia</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="assets/images/profile/<?= $u['profile_pic'] ?>" width="35" height="35" class="rounded-circle shadow-sm">
                            <div>
                                <div class="fw-bold"><?= $u['first_name'].' '.$u['last_name'] ?></div>
                                <div class="text-muted small">@<?= $u['username'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?= $u['ac_status']==1 ? '<span class="status-pill bg-success-subtle text-success">Hoạt động</span>' : '<span class="status-pill bg-danger-subtle text-danger">Bị chặn</span>' ?>
                    </td>
                    <td class="text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="?admin&tab=users&action=<?= $u['ac_status']==1 ? 'block_user' : 'activate_user' ?>&id=<?= $u['id'] ?>" class="btn btn-light border btn-action">
                            <i class="bi bi-<?= $u['ac_status']==1 ? 'slash-circle' : 'check-circle' ?>"></i>
                        </a>
                        <a href="?admin&tab=users&action=delete_user&id=<?= $u['id'] ?>" class="btn btn-light border btn-action text-danger" onclick="return confirm('Xóa người dùng này?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php elseif($tab == 'posts'): ?>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Người đăng</th>
                    <th>Nội dung</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($posts as $p): ?>
                <tr>
                    <td><img src="assets/images/posts/<?= $p['post_img'] ?>" width="50" class="rounded"></td>
                    <td>@<?= $p['username'] ?></td>
                    <td class="text-truncate" style="max-width: 200px;"><?= $p['post_text'] ?></td>
                    <td class="text-end">
                        <a href="?admin&tab=posts&action=delete_post&id=<?= $p['id'] ?>" class="btn btn-light border btn-action text-danger" onclick="return confirm('Xóa bài viết này?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php elseif($tab == 'comments'): ?>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Nội dung bình luận</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($comments as $c): ?>
                <tr>
                    <td>@<?= $c['username'] ?></td>
                    <td><?= $c['comment'] ?></td>
                    <td class="text-end">
                        <a href="?admin&tab=comments&action=delete_comment&id=<?= $c['id'] ?>" class="btn btn-light border btn-action text-danger" onclick="return confirm('Xóa bình luận này?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>