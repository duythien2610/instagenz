<?php
// Kiểm tra và lấy thông tin user từ Session
if (isset($_SESSION['userdata'])) {
    $user = $_SESSION['userdata'];
} else {
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


        <ul class="navbar-nav mb-2 mb-lg-0">

            <li class="nav-item">
                <a class="nav-link text-dark" href="?"><i class="bi bi-house-door-fill"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" data-bs-toggle="modal" data-bs-target="#addpost" href="#"><i class="bi bi-plus-square-fill"></i></a>
            </li>
            
            <div class="dropdown">
                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notif-bell">
                    <i class="bi bi-bell-fill"></i>
                    <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                          style="font-size: 0.6rem; display: <?= ($unread_count > 0) ? 'inline-block' : 'none' ?>">
                        <?= $unread_count ?>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 350px; max-height: 400px; overflow-y: auto;" id="notifications-dropdown">
                    <li class="p-2 border-bottom fw-bold">Thông báo</li>
                    <div id="notifications-list">
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notif): ?>
                                <?php
                                $bg_class = ($notif['read_status'] == 0) ? 'bg-light' : 'bg-white';
                                $notif_id = $notif['id'];
                                $post_id = $notif['post_id'];
                                $username = $notif['username'];
                                ?>
                                <li>
                                    <a class="notification-item dropdown-item d-flex align-items-center p-2 <?= $notif['read_status'] == 0 ? 'unread-notification' : 'read-notification' ?>" 
                                       href="javascript:void(0);" 
                                       data-notif-id="<?= $notif_id ?>"
                                       data-post-id="<?= $post_id ?>"
                                       data-username="<?= htmlspecialchars($username) ?>"
                                       data-read-status="<?= $notif['read_status'] ?>">
                                        <?php if ($notif['read_status'] == 0): ?>
                                            <span class="unread-dot me-2"></span>
                                        <?php endif; ?>
                                        <img src="assets/images/profile/<?= $notif['profile_pic'] ?>" class="rounded-circle border" style="width:40px; height:40px; object-fit:cover">
                                        <div class="ms-2 text-wrap flex-grow-1">
                                            <span class="<?= $notif['read_status'] == 0 ? 'fw-bold' : 'fw-normal' ?>"><?= htmlspecialchars($notif['first_name'] . ' ' . $notif['last_name']) ?></span>
                                            <span class="<?= $notif['read_status'] == 0 ? 'fw-semibold' : '' ?>"><?= htmlspecialchars($notif['message']) ?></span>
                                            <br>
                                            <small class="text-muted"><?= show_time_ago($notif['created_at']) ?></small>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="p-3 text-center text-muted">Không có thông báo nào.</li>
                        <?php endif; ?>
                    </div>
                </ul>
            </div>
            <li class="nav-item position-relative">
                <a class="nav-link text-dark" href="?message">
                    <i class="bi bi-chat-right-dots-fill"></i>
                    <span id="msg-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none; font-size: 0.6rem;">
                        0
                    </span>
                </a>
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
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item theme-toggle-item" href="javascript:void(0);" id="theme-toggle">
                            <i class="bi bi-sun-fill" id="theme-icon"></i> 
                            <span id="theme-text">Light Mode</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="actions.php?logout"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                </ul>
            </li>

        </ul>
    </div>
</nav>

<style>
    /* Style cho thông báo chưa đọc */
    .unread-notification {
        background-color: #e7f3ff !important;
        border-left: 3px solid #0d6efd;
        position: relative;
    }
    
    .unread-notification:hover {
        background-color: #d0e7ff !important;
    }
    
    /* Chấm tròn đánh dấu thông báo chưa đọc */
    .unread-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #0d6efd;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    /* Style cho thông báo đã đọc */
    .read-notification {
        background-color: #ffffff !important;
        border-left: 3px solid transparent;
        opacity: 0.85;
    }
    
    .read-notification:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Đảm bảo notification item có đủ không gian */
    .notification-item {
        transition: all 0.2s ease;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script>
    // 1. Check tin nhắn (Giữ nguyên code cũ)
    function checkUnreadMessages() {
        $.ajax({
            url: 'assets/php/ajax.php?check_unread',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.count > 0) {
                    $('#msg-badge').text(response.count).show();
                } else {
                    $('#msg-badge').hide();
                }
            }
        });
    }

    // 2. [MỚI] Check thông báo Chuông (Follow, Like, Comment)
    function checkNotifications() {
        $.ajax({
            url: 'assets/php/ajax.php?check_general_notifications',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.count > 0) {
                    $('#notif-badge').text(response.count).show();
                } else {
                    $('#notif-badge').hide();
                }
            }
        });
    }

    // 3. Load danh sách thông báo (Real-time)
    function loadNotificationsList() {
        $.ajax({
            url: 'assets/php/ajax.php?get_notifications_list',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#notifications-list').html(response.html);
                }
            },
            error: function() {
                console.log('Error loading notifications');
            }
        });
    }

    // Biến để lưu interval cho việc refresh notifications
    var notificationsRefreshInterval = null;

    $(document).ready(function() {
        checkUnreadMessages();
        checkNotifications();

        // Lặp lại mỗi 2 giây để cập nhật số thông báo
        setInterval(function(){
            checkUnreadMessages();
            checkNotifications();
        }, 2000);

        // Bootstrap 5: Xử lý khi mở dropdown
        var dropdownElement = document.querySelector('#notif-bell');
        if (dropdownElement) {
            dropdownElement.addEventListener('shown.bs.dropdown', function() {
                // Load danh sách ngay khi mở dropdown
                loadNotificationsList();
                
                // Refresh danh sách mỗi 3 giây khi dropdown đang mở
                if (notificationsRefreshInterval) {
                    clearInterval(notificationsRefreshInterval);
                }
                notificationsRefreshInterval = setInterval(function() {
                    loadNotificationsList();
                    checkNotifications(); // Cập nhật số thông báo
                }, 3000);
            });

            dropdownElement.addEventListener('hidden.bs.dropdown', function() {
                if (notificationsRefreshInterval) {
                    clearInterval(notificationsRefreshInterval);
                    notificationsRefreshInterval = null;
                }
            });
        }

        // Xử lý khi click vào thông báo
        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            var $item = $(this);
            var postId = $item.data('post-id');
            var notifId = $item.data('notif-id');
            var username = $item.data('username');
            var readStatus = $item.data('read-status');

            // Chỉ đánh dấu đã đọc nếu thông báo chưa được đọc
            if (notifId && readStatus == 0) {
                // Cập nhật giao diện ngay lập tức (optimistic update)
                $item.removeClass('unread-notification').addClass('read-notification');
                $item.attr('data-read-status', '1');
                $item.find('.unread-dot').remove();
                $item.find('.fw-bold').removeClass('fw-bold').addClass('fw-normal');
                $item.find('.fw-semibold').removeClass('fw-semibold');
                
                // Gửi request đánh dấu đã đọc
                $.ajax({
                    url: 'assets/php/ajax.php?mark_notification_read',
                    method: 'POST',
                    data: { notification_id: notifId },
                    success: function() {
                        // Cập nhật lại số lượng thông báo chưa đọc
                        checkNotifications();
                        // Refresh lại danh sách thông báo để đảm bảo đồng bộ
                        setTimeout(function() {
                            loadNotificationsList();
                        }, 500);
                    },
                    error: function() {
                        // Nếu lỗi, rollback lại giao diện
                        $item.removeClass('read-notification').addClass('unread-notification');
                        $item.attr('data-read-status', '0');
                        $item.prepend('<span class="unread-dot me-2"></span>');
                        $item.find('.fw-normal').removeClass('fw-normal').addClass('fw-bold');
                    }
                });
            }

            // Xử lý điều hướng
            if (postId && postId > 0) {
                // Nếu là thông báo like hoặc comment -> điều hướng đến trang với parameter post_view
                // Script trong footer.php sẽ tự động mở modal
                window.location.href = '?post_view=' + postId;
            } else {
                // Nếu là thông báo follow -> điều hướng đến profile
                window.location.href = '?u=' + username;
            }
        });
    });

    // === DARK/LIGHT MODE TOGGLE ===
    // Make functions global để có thể gọi từ onclick hoặc từ bất kỳ đâu
    window.applyTheme = function(theme) {
        if (!theme) {
            theme = localStorage.getItem('theme') || 'light';
        }
        
        const html = document.documentElement;
        const body = document.body;
        
        // Apply theme ngay lập tức
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            html.setAttribute('data-theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            html.setAttribute('data-theme', 'light');
        }
        
        // Cập nhật UI
        updateThemeUI(theme);
    };

    // Function để cập nhật UI (icon và text)
    function updateThemeUI(theme) {
        const themeIcon = document.getElementById('theme-icon');
        const themeText = document.getElementById('theme-text');
        
        if (themeIcon && themeText) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-moon-fill';
                themeText.textContent = 'Dark Mode';
            } else {
                themeIcon.className = 'bi bi-sun-fill';
                themeText.textContent = 'Light Mode';
            }
        }
    }

    // Function để toggle theme - Make global
    window.toggleTheme = function() {
        const body = document.body;
        const isDark = body.classList.contains('dark-mode');
        const newTheme = isDark ? 'light' : 'dark';
        
        window.applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
        
        console.log('Theme switched to:', newTheme);
        
        // Đóng dropdown
        setTimeout(function() {
            var dropdownElement = document.getElementById('navbarDropdown');
            if (dropdownElement) {
                var dropdown = bootstrap.Dropdown.getInstance(dropdownElement);
                if (dropdown) {
                    dropdown.hide();
                }
            }
        }, 100);
    };

    // Load theme khi trang load
    $(document).ready(function() {
        // Áp dụng theme đã lưu và cập nhật UI
        const savedTheme = localStorage.getItem('theme') || 'light';
        window.applyTheme(savedTheme);
        
        // Event listener cho theme toggle - sử dụng cả onclick và event listener
        $('#theme-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.toggleTheme();
        });
    });
    
    // Apply theme ngay khi script load (không đợi jQuery ready)
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (document.body) {
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.body.classList.remove('dark-mode');
                document.documentElement.setAttribute('data-theme', 'light');
            }
        }
    })();
</script>