<nav class="admin-nav">
    <ul class="admin-menu">
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=dashboard" <?= $page === 'dashboard' ? 'class="active"' : '' ?>>
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=stations" <?= $page === 'stations' ? 'class="active"' : '' ?>>
                <i class="fas fa-charging-station"></i> Stations
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=users" <?= $page === 'users' ? 'class="active"' : '' ?>>
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=bookings" <?= $page === 'bookings' ? 'class="active"' : '' ?>>
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=reports" <?= $page === 'reports' ? 'class="active"' : '' ?>>
                <i class="fas fa-chart-line"></i> Reports
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/admin.php?page=maintenance" <?= $page === 'maintenance' ? 'class="active"' : '' ?>>
                <i class="fas fa-tools"></i> Maintenance
            </a>
        </li>
    </ul>
</nav>

<style>
    .admin-nav {
        padding: 1rem;
    }

    .admin-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .admin-menu li {
        margin-bottom: 0.5rem;
    }

    .admin-menu a {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--gray-700);
        text-decoration: none;
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }

    .admin-menu a:hover {
        background-color: var(--gray-100);
        color: var(--primary);
    }

    .admin-menu a.active {
        background-color: var(--primary);
        color: var(--white);
    }

    .admin-menu a i {
        width: 20px;
        margin-right: 0.75rem;
        text-align: center;
    }
</style>