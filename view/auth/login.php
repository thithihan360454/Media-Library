<?php

$pageTitle = 'Login';
$section = 'login';
$hideSearch = true;

require BASE_PATH . '/view/Layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['old']);

?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/register.css">

<div class="section page">

    <div class="wrapper">

        <div class="auth-container">

            <h2>Login</h2>

            <!-- SUCCESS -->
            <?php if (!empty($_SESSION['success'])): ?>

                <div class="auth-message auth-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <!-- AUTH ERROR -->
            <?php if (!empty($_SESSION['auth_error'])): ?>

                <div class="auth-message auth-error">
                    <?= htmlspecialchars($_SESSION['auth_error']) ?>
                </div>

                <?php unset($_SESSION['auth_error']); ?>

            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/Public/index.php?page=login-submit">

                <!-- EMAIL -->
                <div class="auth-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="text"
                        name="email"
                        id="email"
                        placeholder="Enter your email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>">

                    <!-- EMAIL ERROR -->
                    <?php if (!empty($errors['email'])): ?>

                        <small class="auth-error">
                            <?= htmlspecialchars($errors['email']) ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- PASSWORD -->
                <div class="auth-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password">

                    <!-- PASSWORD ERROR -->
                    <?php if (!empty($errors['password'])): ?>

                        <small class="auth-error">
                            <?= htmlspecialchars($errors['password']) ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="auth-button">

                    Login

                </button>

            </form>

            <!-- REGISTER -->
            <div class="auth-footer">

                Don't have an account?

                <a href="<?= BASE_URL ?>/Public/index.php?page=register">
                    Register here
                </a>

            </div>

        </div>

    </div>

</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>