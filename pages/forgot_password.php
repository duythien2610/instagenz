<div class="login">
    <div class="col-4 bg-white border rounded p-4 shadow-sm">
        <?php
        // TRƯỜNG HỢP 1: NHẬP MÃ OTP
        if (isset($_GET['verify'])):
        ?>
            <form method="post" action="actions.php?verify_forgot_code">
                <div class="d-flex justify-content-center">
                    <img class="mb-4" src="assets/images/pictogram.png" alt="" height="45">
                </div>
                <h1 class="h5 mb-3 fw-normal">Verify OTP</h1>

                <p class="text-muted">Mã xác thực đã được gửi tới email: <b><?= $_SESSION['forgot_email'] ?? '' ?></b></p>

                <div class="form-floating mt-1">
                    <input type="text" name="code" class="form-control rounded-0" placeholder="Nhập mã 6 số" required>
                    <label for="floatingInput">Enter Code</label>
                </div>
                <?= showError('email_verify') ?>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" type="submit">Verify Code</button>
                    <a href="index.php?forgotpassword" class="text-decoration-none">Back</a>
                </div>
            </form>

        <?php
        // TRƯỜNG HỢP 2: ĐỔI MẬT KHẨU MỚI
        elseif (isset($_GET['changepass'])):
        ?>
            <form method="post" action="actions.php?changepassword">
                <div class="d-flex justify-content-center">
                    <img class="mb-4" src="assets/images/pictogram.png" alt="" height="45">
                </div>
                <h1 class="h5 mb-3 fw-normal">New Password</h1>

                <div class="form-floating mt-1">
                    <input type="password" name="password" class="form-control rounded-0" placeholder="New Password" required>
                    <label for="floatingInput">Enter New Password</label>
                </div>
                <?= showError('password') ?>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" type="submit">Change Password</button>
                </div>
            </form>

        <?php
        // TRƯỜNG HỢP 3 (MẶC ĐỊNH): NHẬP EMAIL ĐỂ LẤY MÃ
        else:
        ?>
            <form method="post" action="actions.php?forgotpassword">
                <div class="d-flex justify-content-center">
                    <img class="mb-4" src="assets/images/pictogram.png" alt="" height="45">
                </div>
                <h1 class="h5 mb-3 fw-normal">Forgot Password</h1>

                <div class="form-floating mt-1">
                    <input type="email" name="email" class="form-control rounded-0" placeholder="username/email" required>
                    <label for="floatingInput">Enter your email</label>
                </div>
                <?= showError('email') ?>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" type="submit">Send OTP</button>
                    <a href="?login" class="text-decoration-none">Back to Login</a>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>