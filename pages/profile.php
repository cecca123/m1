<?php
// Set page title
$pageTitle = 'Profile Settings';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth-functions.php';

// Require login
requireLogin();

// Get current user data
$userId = $_SESSION['user_id'];
$user = getCurrentUser1();

if (!$user) {
    setFlashMessage('error', 'User not found.');
    redirect('pages/dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    // Validate CSRF token
    if (!validateCsrfToken($csrf)) {
        setFlashMessage('error', 'Invalid request. Please try again.');
        redirect('profile.php');
    }

    // Initialize PDO connection
    $pdo = getDbConnection();
    $errors = [];

    // Validate and update name and email
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } elseif ($email !== $user['email']) {
        // Check if new email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            $errors[] = 'Email address is already in use.';
        }
    }

    // Handle password update if provided
    if (!empty($currentPassword)) {
        if (!password_verify($currentPassword, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (empty($newPassword)) {
            $errors[] = 'New password is required when updating password.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        } elseif (!isPasswordStrong($newPassword)) {
            $errors[] = 'New password must be at least 8 characters long and include uppercase, lowercase, number, and special character.';
        }
    }

    // Update user data if no errors
    if (empty($errors)) {
        try {
            $pdo->autocommit(false);

            // Update basic info
            $sql = "UPDATE users SET name = ?, email = ?";
            $params = [$name, $email];

            // Add password update if provided
            if (!empty($newPassword)) {
                $sql .= ", password = ?";
                $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE user_id = ?";
            $params[] = $userId;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $pdo->commit();
            $pdo->autocommit(true);

            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            setFlashMessage('success', 'Profile updated successfully.');
            redirect('pages/profile.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Profile update error: " . $e->getMessage());
            setFlashMessage('error', 'An error occurred while updating your profile.');
        }
    } else {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// Generate CSRF token
$csrfToken = generateCsrfToken();

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Profile Settings</h1>
        <p class="page-subtitle">Manage your account information</p>
    </div>

    <div class="profile-container">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/pages/profile.php" class="needs-validation">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="form-section">
                        <h3>Basic Information</h3>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Change Password</h3>
                        <p class="text-muted">Leave blank to keep your current password</p>

                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control"
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}">
                            <small class="form-text">
                                Password must be at least 8 characters long and include uppercase, lowercase,
                                number, and special character.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
   .profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: var(--space-8) var(--space-4);
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    transition: box-shadow var(--transition-fast);
}
.profile-container:hover {
    box-shadow: var(--shadow-xl);
}
.card {
    background: var(--gray-100);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    padding: var(--space-6);
    border: none;
}
.card-body {
    padding: 0;
}
.form-group {
    margin-bottom: var(--space-5);
}
.form-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--space-2);
    display: block;
}
.form-control {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    background: var(--white);
    font-size: 1rem;
    transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}
.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-light);
    outline: none;
}
.btn-primary {
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    color: var(--white);
    border: none;
    border-radius: var(--radius-full);
    padding: var(--space-3) var(--space-8);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: var(--shadow-sm);
    transition: background var(--transition-fast), box-shadow var(--transition-fast);
}
.btn-primary:hover {
    background: linear-gradient(90deg, var(--primary-dark), var(--primary));
    box-shadow: var(--shadow-md);
}
.form-section {
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
}
.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.form-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: var(--space-4);
    color: var(--primary);
}
.form-text {
    color: var(--gray-500);
    font-size: 0.95rem;
}
.page-header {
    margin-bottom: var(--space-8);
    text-align: center;
}
.page-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: var(--space-2);
}
.page-subtitle {
    font-size: 1.1rem;
    color: var(--gray-600);
}
</style>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>