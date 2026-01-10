<?php
// Kiểm tra và lấy thông tin user từ Session
if (isset($_SESSION['userdata'])) {
    $user = $_SESSION['userdata'];
} else {
    $user = array(
        'profile_pic' => 'default_profile.jpg',
        'first_name' => '',
        'last_name' => '',
        'gender' => 1,
        'email' => '',
        'username' => ''
    );
}
?>

<link rel="stylesheet" href="assets/css/editprofilestyle.css">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Edit Profile</h2>
                    </div>

                    <form method="post" action="actions.php?update_profile" enctype="multipart/form-data">
                        
                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill"></i> Profile updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } ?>

                        <div class="d-flex flex-column align-items-center mb-4">
                            <img src="assets/images/profile/<?= $user['profile_pic'] ?>" 
                                 id="profile_img_pre" 
                                 class="rounded-circle border border-3 border-light shadow-sm object-fit-cover mb-3" 
                                 style="width: 150px; height: 150px;" 
                                 alt="Profile Picture">
                            
                            <div class="col-10 col-md-8">
                                <label for="formFile" class="form-label text-muted small text-center w-100">Change Profile Picture</label>
                                <input class="form-control form-control-sm" type="file" name="profile_pic" id="formFile" accept="image/*">
                            </div>
                            <?= showError('profile_pic') ?>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="first_name" value="<?= $user['first_name'] ?>" class="form-control" id="firstNameInput" placeholder="First Name">
                                    <label for="firstNameInput">First Name</label>
                                </div>
                                <?= showError('first_name') ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="last_name" value="<?= $user['last_name'] ?>" class="form-control" id="lastNameInput" placeholder="Last Name">
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                                <?= showError('last_name') ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small">Gender</label>
                            <div class="d-flex gap-4 p-2 border rounded bg-light justify-content-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender_display" id="maleRadio"
                                        <?= $user['gender'] == 1 ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="maleRadio">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender_display" id="femaleRadio"
                                        <?= $user['gender'] == 2 ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="femaleRadio">Female</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender_display" id="otherRadio"
                                        <?= $user['gender'] == 0 ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="otherRadio">Other</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" value="<?= $user['email'] ?>" class="form-control bg-light" id="emailInput" placeholder="name@example.com" disabled readonly>
                            <label for="emailInput">Email (Locked)</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" value="<?= $user['username'] ?>" name="username" class="form-control" id="usernameInput" placeholder="Username">
                            <label for="usernameInput">Username</label>
                            <?= showError('username') ?>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="New Password">
                            <label for="passwordInput">New Password</label>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg fw-bold rounded-3" type="submit">Update Profile</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview ảnh ngay sau khi chọn
    document.getElementById('formFile').onchange = evt => {
        const [file] = document.getElementById('formFile').files
        if (file) {
            document.getElementById('profile_img_pre').src = URL.createObjectURL(file)
        }
    }
</script>