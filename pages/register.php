<?php
// Set page title
$pageTitle = 'Register';

// Include configuration
require_once dirname(__DIR__) . '/config/config.php';
require_once INCLUDES_PATH . 'auth-functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validateCsrfToken($csrf)) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        redirect('register.php');
    }

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        setFlashMessage('error', 'Please fill in all required fields.');
    } elseif ($password !== $confirm_password) {
        setFlashMessage('error', 'Passwords do not match.');
    } elseif (!isPasswordStrong($password)) {
        setFlashMessage('error', 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Please enter a valid email address.');
    } else {    
        $userId = registerUser($name, $email, $password);
        if ($userId) {
            loginUser($email, $password);
            setFlashMessage('success', 'Registrazione effettuata con successo!');
            redirect('pages/dashboard.php');
        } else {
            setFlashMessage('error', 'Email già registrata. Usa un’altra email oppure accedi.');
        }
    }
}

// Generate CSRF token
$csrfToken = generateCsrfToken();

// Include header
require_once dirname(__DIR__) . '/includes/header.php';

// Mostra messaggi flash
if ($success = getFlashMessage('success')) {
    echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
}
if ($error = getFlashMessage('error')) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Create a New Account</h2>
            <p>Fill in your details to register</p>
        </div>

        <div class="auth-body">
            <form method="POST" action="<?= APP_URL ?>/pages/register.php" class="needs-validation">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required autofocus
                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}">
                    <small class="form-text">
                        Password must be at least 8 characters long and include uppercase, lowercase,
                        number, and special character.
                    </small>
                </div>

                <div class="form-group mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?= APP_URL ?>/pages/login.php">Login</a></p>
        </div>
    </div>
</div>

<style>
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - var(--header-height) - var(--footer-height) - var(--space-16));
        padding: var(--space-6) 0;
    }

    .auth-card {
        width: 100%;
        max-width: 450px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .auth-header {
        padding: var(--space-6);
        background-color: var(--primary);
        color: var(--white);
        text-align: center;
    }

    .auth-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: var(--space-2);
    }

    .auth-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    .auth-body {
        padding: var(--space-6);
    }

    .auth-footer {
        padding: var(--space-4) var(--space-6);
        border-top: 1px solid var(--gray-200);
        text-align: center;
        background-color: var(--gray-200);
    }

    .auth-footer p {
        margin-bottom: 0;
        color: var(--gray-600);
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }
</style>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>
