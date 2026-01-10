<?php
    include_once "header.php";
?>

<div class="login-container">
    
    <div class="login-card">
        
        <form method="post" action="actions.php?login">
            <div class="text-center">
                <img class="login-logo" src="assets/images/instagenz.png" alt="" height="100">
            </div>

            <?php if (isset($_GET['verified'])): ?>
                <div class="alert alert-success text-center p-2" style="font-size:12px;">Xác thực thành công!<br>Bạn có thể đăng nhập</div>
            <?php endif; ?>
            <?php if (isset($_GET['changed_pass'])): ?>
                <div class="alert alert-success text-center p-2" style="font-size:12px;">Đổi mật khẩu thành công!<br>Hãy đăng nhập lại.</div>
            <?php endif; ?>

            <p class="title">Please Sign In</p>

            <div class="form-floating mb-2">
                <input type="text" name="username_email" value="<?= showFormData('username_email') ?>" class="form-control" id="floatingInput" placeholder="username/email">
                <label for="floatingInput">Username or Email</label>
            </div>
            <?= showError('username_email') ?>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPass word">Password</label>
                <i class="bi bi-eye-slash eye-toggle" id="togglePassword"></i>
            </div>
            <?= showError('password') ?>
            <?= showError('checkuser') ?>

            <button class="btn btn-insta" type="submit">Log in</button>

            <div class="divider-text">OR</div>

            <div class="text-center mt-3">
                <a href="?forgotpassword&newfp" class="link-secondary">Forgot password?</a>
            </div>
            
            <div class="text-center mt-4 pt-3 border-top">
                <span style="font-size:14px; color:#262626;">Don't have an account?</span> 
                <a href="?signup" class="link-signup">Sign up</a>
            </div>

        </form>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#floatingPassword');

    if(togglePassword) {
        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Nếu bạn dùng FontAwesome (fas) thì đổi class khác, ở đây tôi dùng Bootstrap Icons (bi)
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }
</script>

</body>
</html>