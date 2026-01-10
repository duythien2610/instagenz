<?php
// Khu vực ADMIN tách biệt hoàn toàn với mạng xã hội
session_start();

require_once 'includes/db.php';

// Cấu hình tài khoản admin riêng (có thể chỉnh sửa cho phù hợp)
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = 'admin123'; // Đổi mật khẩu tuỳ ý

// Đăng xuất admin
if (isset($_GET['logout'])) {
    unset($_SESSION['AdminAuth']);
    unset($_SESSION['AdminUser']);
    header('Location: admin.php');
    exit;
}

// Xử lý login admin
$login_error = '';
if (isset($_POST['admin_login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === $ADMIN_USERNAME && $pass === $ADMIN_PASSWORD) {
        $_SESSION['AdminAuth'] = true;
        $_SESSION['AdminUser'] = ['username' => $user];
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Sai tài khoản hoặc mật khẩu admin';
    }
}

// Nếu chưa đăng nhập admin -> hiển thị form login riêng
if (!isset($_SESSION['AdminAuth']) || $_SESSION['AdminAuth'] !== true):
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - InstaGenz</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f6f9;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .admin-card{
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="card admin-card border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="assets/images/instagenz.png" alt="InstaGenz" style="height:60px; object-fit:contain;">
                <h4 class="mt-3 mb-0">Admin Panel</h4>
                <small class="text-muted">Đăng nhập quản trị hệ thống</small>
            </div>
            <?php if($login_error): ?>
                <div class="alert alert-danger py-2"><?= $login_error ?></div>
            <?php endif; ?>
            <form method="post" action="admin.php">
                <div class="mb-3">
                    <label class="form-label">Tài khoản admin</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary w-100">
                    Đăng nhập
                </button>
            </form>
        </div>
    </div>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Kết thúc phần login
exit;
endif;

// ====== PHẦN DASHBOARD ADMIN (chỉ chạy khi đã đăng nhập AdminAuth) ======

function admin_get_single_count($query)
{
    global $db;
    $run = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($run);
    return (int) $row['c'];
}

$total_users    = admin_get_single_count("SELECT COUNT(*) AS c FROM users");
$total_posts    = admin_get_single_count("SELECT COUNT(*) AS c FROM posts");
$total_comments = admin_get_single_count("SELECT COUNT(*) AS c FROM comments");
$total_likes    = admin_get_single_count("SELECT COUNT(*) AS c FROM likes");

$users_sql = "
    SELECT 
        u.*,
        (SELECT COUNT(*) FROM posts p WHERE p.user_id = u.id) AS post_count,
        (SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id) AS comment_count,
        (SELECT COUNT(*) FROM likes l WHERE l.user_id = u.id) AS like_count
    FROM users u
    ORDER BY u.id ASC
";
$users_run = mysqli_query($db, $users_sql);
$users     = mysqli_fetch_all($users_run, MYSQLI_ASSOC);

// Xử lý activate/block từ admin panel
if (isset($_GET['activate']) && !empty($_GET['activate'])) {
    $uid = (int) $_GET['activate'];
    mysqli_query($db, "UPDATE users SET ac_status = 1 WHERE id = $uid");
    header('Location: admin.php');
    exit;
}

if (isset($_GET['block']) && !empty($_GET['block'])) {
    $uid = (int) $_GET['block'];
    mysqli_query($db, "UPDATE users SET ac_status = 2 WHERE id = $uid");
    header('Location: admin.php');
    exit;
}

$adminUser = $_SESSION['AdminUser']['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InstaGenz</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            border-radius: .25rem;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        .stat-card {
            border-radius: .75rem;
            color: #fff;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            font-size: 2rem;
            opacity: .9;
        }
        .table-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #dee2e6;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <nav class="col-md-2 d-none d-md-block sidebar py-4">
            <div class="px-3 mb-4 text-center">
                <img src="assets/images/instagenz.png" alt="InstaGenz" style="height:50px; object-fit:contain;" class="mb-2">
                <h6 class="mb-0"><?= htmlspecialchars($adminUser) ?></h6>
                <small class="text-muted">Administrator</small>
            </div>
            <ul class="nav flex-column px-2">
                <li class="nav-item mb-1">
                    <a class="nav-link active" href="admin.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="admin.php?logout=1">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- MAIN -->
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Dashboard</h3>
            </div>

            <!-- STAT CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" style="background:#0d6efd;">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h3 class="mb-0"><?= $total_users ?></h3>
                            <small>Total Users</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" style="background:#198754;">
                        <div class="stat-icon"><i class="bi bi-card-image"></i></div>
                        <div>
                            <h3 class="mb-0"><?= $total_posts ?></h3>
                            <small>Total Posts</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" style="background:#ffc107;">
                        <div class="stat-icon"><i class="bi bi-chat-left-text-fill"></i></div>
                        <div>
                            <h3 class="mb-0"><?= $total_comments ?></h3>
                            <small>Total Comments</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" style="background:#dc3545;">
                        <div class="stat-icon"><i class="bi bi-heart-fill"></i></div>
                        <div>
                            <h3 class="mb-0"><?= $total_likes ?></h3>
                            <small>Total Likes</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- USER LIST -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Lists</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">#No</th>
                                    <th>User</th>
                                    <th class="text-center" style="width:120px">Posts</th>
                                    <th class="text-center" style="width:140px">Comments</th>
                                    <th class="text-center" style="width:120px">Likes</th>
                                    <th class="text-center" style="width:130px">Status</th>
                                    <th class="text-center" style="width:200px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($users as $u):
                                    $status_label = 'Chưa xác thực';
                                    $status_class = 'secondary';
                                    if ($u['ac_status'] == 1) {
                                        $status_label = 'Hoạt động';
                                        $status_class = 'success';
                                    } elseif ($u['ac_status'] == 2) {
                                        $status_label = 'Bị chặn';
                                        $status_class = 'danger';
                                    }
                                ?>
                                <tr>
                                    <td>#<?= $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/profile/<?= htmlspecialchars($u['profile_pic']) ?>" class="table-avatar me-2" alt="">
                                            <div>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                                    - @<?= htmlspecialchars($u['username']) ?>
                                                </div>
                                                <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $u['post_count'] ?></td>
                                    <td class="text-center"><?= $u['comment_count'] ?></td>
                                    <td class="text-center"><?= $u['like_count'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $status_class ?>"><?= $status_label ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="admin.php?activate=<?= $u['id'] ?>" class="btn btn-sm btn-success">
                                            Login User
                                        </a>
                                        <a href="admin.php?block=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Chặn user này?');">
                                            Block
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($users) < 1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Chưa có user nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
