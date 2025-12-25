<div class="login">
    <div class="col-4 bg-white border rounded p-4 shadow-sm">
        <form method="post" action="actions.php?signup">
            <div class="d-flex justify-content-center">

                <img class="mb-4" src="assets/images/instagenz.png" alt="" height="60">
            </div>
            <h1 class="h5 mb-3 fw-normal">Create new account</h1>
            <div class="d-flex">
                <div class="form-floating mt-1 col-6 ">
                    <input type="text" name="first_name" value="<?= showFormData('first_name') ?>" class="form-control rounded-0" placeholder="username/email" required>
                    <label for="floatingInput">first name</label>
                </div>
                <div class="form-floating mt-1 col-6">
                    <input type="text" name="last_name" value="<?= showFormData('last_name') ?>" class="form-control rounded-0" placeholder="username/email" required>
                    <label for="floatingInput">last name</label>
                </div>
            </div>
            <?= showError('first_name') ?>
            <?= showError('last_name') ?>
            <div class="d-flex gap-3 my-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender1" required
                        value="1" <?= (showFormData('gender') == 1 || showFormData('gender') == '') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gender1">
                        Nam
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender2" required
                        value="2" <?= showFormData('gender') == 2 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gender2">
                        Nữ
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender0" required
                        value="0" <?= showFormData('gender') == 0 && showFormData('gender') != '' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gender0">
                        Khác
                    </label>
                </div>
            </div>
            <div class="form-floating mt-1">
                <input type="email" name="email" value="<?= showFormData('email') ?>" class="form-control rounded-0" placeholder="username/email" required>
                <label for="floatingInput">email</label>
            </div>
            <?= showError('email') ?>
            <div class="form-floating mt-1">
                <input type="text" name="username" value="<?= showFormData('username') ?>" class="form-control rounded-0" placeholder="username/email" required>
                <label for="floatingInput">username</label>
            </div>
            <?= showError('username') ?>
            <div class="form-floating mt-1">
                <input type="password" name="password" class="form-control rounded-0" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">password</label>
            </div>
            <?= showError('password') ?>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" type="submit">Sign Up</button>
                <a href="?login" class="text-decoration-none">Already have an account ?</a>


            </div>

        </form>
    </div>
</div>


<script src="bootstrap/js/bootstrap.bundle.min.js"></script>

</html>