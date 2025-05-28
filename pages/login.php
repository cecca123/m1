<?php
// Set page title
$pageTitle = 'Login';

// Include configuration
require_once dirname(__DIR__) . '/config/config.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $csrf = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Validate CSRF token
    if (!validateCsrfToken($csrf)) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        redirect('login.php');
    }

    // Validate required fields
    if (empty($email) || empty($password)) {
        setFlashMessage('error', 'Please enter both email and password.');
    } else {
        // Get database connection (PDO)
        $pdo = getDbConnection();

        // Prepare statement (PDO)
        $stmt = $pdo->prepare("SELECT user_id, name, email, password FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // Redirect to dashboard
                setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
                redirect('pages/dashboard.php');
            } else {
                setFlashMessage('error', 'Invalid email or password. Please try again.');
            }
        } else {
            setFlashMessage('error', 'Invalid email or password. Please try again.');
        }
    }
}

// Generate CSRF token
$csrfToken = generateCsrfToken();

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Login to Your Account</h2>
            <p>Enter your credentials to access your account</p>
        </div>

        <div class="auth-body">
            <form method="POST" action="<?= APP_URL ?>/pages/login.php" class="needs-validation">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group form-check mb-4">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Remember me</label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            <p>Don't have an account? <a href="<?= APP_URL ?>/pages/register.php">Register Now</a></p>
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
</style>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>
