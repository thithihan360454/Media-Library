<?php

$pageTitle = 'Register';
$section = 'register';
$hideSearch = true;

require BASE_PATH . '/view/Layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/register.css">

<div class="section page">

    <div class="wrapper">

        <div class="auth-container">

            <h2>Register</h2>

            <!-- SUCCESS -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="auth-message auth-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- ERROR -->
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="auth-message auth-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/Public/index.php?page=register-submit">

                <!-- USERNAME -->
                <div class="auth-group">
                    <label>Username</label>
                    <input type="text" name="username"
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>">

                    <?php if (!empty($errors['username'])): ?>
                        <small class="error"><?= $errors['username'] ?></small>
                    <?php endif; ?>
                </div>

                <!-- EMAIL -->
                <div class="auth-group">
                    <label>Email</label>
                    <input type="text" name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>">

                    <?php if (!empty($errors['email'])): ?>
                        <small class="error"><?= $errors['email'] ?></small>
                    <?php endif; ?>
                </div>

                <!-- PASSWORD -->
                <div class="auth-group">
                    <label>Password</label>

                    <div class="input-wrapper">
                        <input type="password" name="password" id="password">

                        <span class="toggle-password" data-target="password">👁</span>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <small class="error"><?= $errors['password'] ?></small>
                    <?php endif; ?>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="auth-group">
                    <label>Confirm Password</label>

                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password">

                        <span class="toggle-password" data-target="confirm_password">👁</span>
                    </div>

                    <?php if (!empty($errors['confirm_password'])): ?>
                        <small class="error"><?= $errors['confirm_password'] ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="auth-button">
                    Register
                </button>

            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="<?= BASE_URL ?>/Public/index.php?page=login">Login</a>
            </div>

        </div>

    </div>

</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>