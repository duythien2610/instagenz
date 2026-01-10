<?php
    include_once "header.php";
?>

<div class="login-container">
    
    <div class="login-card">
        <form method="post" action="actions.php?signup">
            
            <div class="text-center">
                <img class="login-logo" src="assets/images/instagenz.png" alt="" height="100">
            </div>
            
            <h1 class="title">Sign up to see photos and videos from your friends.</h1>

            <div class="name-row mb-1">
                <div class="form-floating">
                    <input type="text" name="first_name" value="<?= showFormData('first_name') ?>" class="form-control" placeholder="First Name" required>
                    <label>First Name</label>
                </div>
                <div class="form-floating">
                    <input type="text" name="last_name" value="<?= showFormData('last_name') ?>" class="form-control" placeholder="Last Name" required>
                    <label>Last Name</label>
                </div>
            </div>
            <div class="mb-2">
                <?= showError('first_name') ?>
                <?= showError('last_name') ?>
            </div>

            <div class="form-floating mb-2">
                <input type="email" name="email" value="<?= showFormData('email') ?>" class="form-control" placeholder="Email" required>
                <label>Email address</label>
            </div>
            <?= showError('email') ?>

            <div class="form-floating mb-2">
                <input type="text" name="username" value="<?= showFormData('username') ?>" class="form-control" placeholder="Username" required>
                <label>Username</label>
            </div>
            <?= showError('username') ?>

            <div class="form-floating mb-2">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label>Password</label>
            </div>
            <?= showError('password') ?>

            <div class="gender-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender1" value="1" <?= (showFormData('gender') == 1 || showFormData('gender') == '') ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="gender1">Male</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender2" value="2" <?= showFormData('gender') == 2 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gender2">Female</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender0" value="0" <?= showFormData('gender') == 0 && showFormData('gender') != '' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gender0">Other</label>
                </div>
            </div>

            <p class="text-muted text-center" style="font-size: 12px; line-height: 1.4;">
                People who use our service may have uploaded your contact information to Instagram.
            </p>

            <button class="btn btn-insta" type="submit">Sign Up</button>
            
        </form>
    </div>

    <div class="login-card-footer">
        Have an account? <a href="?login" class="link-signup">Log in</a>
    </div>

</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>