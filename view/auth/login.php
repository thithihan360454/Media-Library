<?php

$pageTitle = 'Login';
$section = 'login';
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

            <h2>Login</h2>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="auth-message auth-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['auth_error'])): ?>
                <div class="auth-message auth-error">
                    <?= htmlspecialchars($_SESSION['auth_error']) ?>
                </div>
                <?php unset($_SESSION['auth_error']); ?>
            <?php endif; ?>

            <form method="POST"
                action="<?= BASE_URL ?>/Public/index.php?page=login-submit">

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
                        <input type="password" name="password" id="login_password">

                        <span class="toggle-password" data-target="login_password">👁</span>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <small class="error"><?= $errors['password'] ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="auth-button">
                    Login
                </button>

            </form>

            <div class="auth-footer">
                Don't have an account?
                <a href="<?= BASE_URL ?>/Public/index.php?page=register">Register</a>
            </div>

        </div>

    </div>

</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>