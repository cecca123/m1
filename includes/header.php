<?php
// Start output buffering
ob_start();

// Include configuration
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= APP_URL ?>/assets/images/favicon.ico" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<div class="app-container">
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?= APP_URL ?>">
                        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="<?= APP_NAME ?>">
                        <span><?= APP_NAME ?></span>
                    </a>
                </div>

                <div class="nav-toggle">
                    <button id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <nav class="main-nav">
                    <ul>
                        <li><a href="<?= APP_URL ?>">Home</a></li>
                        <li><a href="<?= APP_URL ?>/pages/stations.php">Stations</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="<?= APP_URL ?>/pages/bookings.php">Bookings</a></li>
                            <li><a href="<?= APP_URL ?>/pages/dashboard.php">Dashboard</a></li>

                            <li><a href="<?= APP_URL ?>/admin/admin.php">Admin</a></li>

                            <li><a href="<?= APP_URL ?>/pages/profile.php">Profile</a></li>
                            <li><a href="<?= APP_URL ?>/pages/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="<?= APP_URL ?>/pages/login.php">Login</a></li>
                            <li><a href="<?= APP_URL ?>/pages/register.php" class="btn btn-primary">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php $flashMessage = getFlashMessage(); ?>
    <?php if ($flashMessage): ?>
        <div class="flash-message flash-<?= $flashMessage['type'] ?>">
            <div class="container">
                <p><?= $flashMessage['message'] ?></p>
                <button class="close-flash"><i class="fas fa-times"></i></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="app-main">