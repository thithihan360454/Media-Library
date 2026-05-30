<?php

$pageTitle = 'Register';
$section = 'register';
$hideSearch = true;

require BASE_PATH . '/view/Layout/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['error'], $_SESSION['success']);

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
            <?php endif; ?>

            <!-- FORM -->
            <form method="POST" action="<?= BASE_URL ?>/Public/index.php?page=register-submit">

                <!-- USERNAME -->
                <div class="auth-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter a username"
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>">

                    <?php if (!empty($errors['username'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['username']) ?></small>
                    <?php endif; ?>
                </div>

                <!-- EMAIL -->
                <div class="auth-group">
                    <label>Email</label>
                    <input type="text" name="email" placeholder="Enter your email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>">

                    <?php if (!empty($errors['email'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <!-- PASSWORD -->
                <div class="auth-group">
                    <label>Password</label>

                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Enter a password">
                        <span class="toggle-password" data-target="password">👁</span>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['password']) ?></small>
                    <?php endif; ?>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="auth-group">
                    <label>Confirm Password</label>

                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password">
                        <span class="toggle-password" data-target="confirm_password">👁</span>
                    </div>

                    <?php if (!empty($errors['confirm_password'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['confirm_password']) ?></small>
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