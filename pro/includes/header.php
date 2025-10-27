<?php
if (!isset($page_title)) {
    $page_title = "سیستم مدیریت مالی - داشبورد";
}

$theme = $_SESSION['theme'] ?? 'default';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="css/style07.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/font-face.css" rel="stylesheet">
</head>

<body data-theme="<?= $theme ?>">
    <!-- Enhanced Header Section -->
    <header class="main-header glass">
        <div class="header-container">
            <!-- Logo Section -->
            <div class="header-section logo-section">
                <a href="home.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="logo-text">مدیریت مالی</span>
                </a>
            </div>

            <!-- Search Section -->
            <div class="header-section search-section">
                <div class="search-container">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="text" name="q" placeholder="جستجو در تراکنش‌ها، گزارشات..." class="search-input glass">
                        <button type="submit" class="search-btn" aria-label="جستجو">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Actions Section -->
            <div class="header-section actions-section">
                <div class="header-actions">
                    <!-- Mobile Search Toggle -->
                    <button class="header-action mobile-search-toggle glass" id="mobileSearchToggle" aria-label="جستجو">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Notifications -->
                    <?php if (($_SESSION['user_type'] ?? '') === 'manager' || ($_SESSION['user_type'] ?? '') === 'owner'): ?>
                        <a href="notifications.php" class="header-action notification-btn glass" aria-label="اعلان‌ها">
                            <i class="fas fa-bell"></i>
                            <?php if (isset($unread_notifications) && $unread_notifications > 0): ?>
                                <span class="badge pulse"><?= $unread_notifications ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <!-- Messages -->
                    <a href="messages.php" class="header-action message-btn glass" aria-label="پیام‌ها">
                        <i class="fas fa-envelope"></i>
                        <?php if (isset($unread_messages) && $unread_messages > 0): ?>
                            <span class="badge pulse"><?= $unread_messages ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Theme Toggle -->
                    <div class="theme-selector">
                        <button class="header-action theme-toggle glass" id="themeToggle" aria-label="تغییر تم">
                            <i class="fas fa-palette"></i>
                        </button>
                        <div class="theme-menu glass-card">
                            <button data-theme="default" class="theme-option <?= $theme === 'default' ? 'active' : '' ?>">
                                <i class="fas fa-sun"></i>
                                تم روشن
                            </button>
                            <button data-theme="dark" class="theme-option <?= $theme === 'dark' ? 'active' : '' ?>">
                                <i class="fas fa-moon"></i>
                                تم تاریک
                            </button>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="user-menu">
                        <button class="user-toggle glass" id="userToggle" aria-label="منوی کاربر">
                            <div class="user-avatar">
                                <?php if (isset($user['avatar']) && !empty($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="عکس پروفایل <?= htmlspecialchars($user['full_name'] ?? 'کاربر') ?>">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <?= mb_substr($user['full_name'] ?? 'کاربر', 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? 'کاربر') ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="user-dropdown glass-card" id="userDropdown">
                            <div class="dropdown-header">
                                <div class="user-avatar large">
                                    <?php if (isset($user['avatar']) && !empty($user['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="عکس پروفایل">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= mb_substr($user['full_name'] ?? 'کاربر', 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info">
                                    <h4><?= htmlspecialchars($user['full_name'] ?? 'کاربر') ?></h4>
                                    <span class="user-role">
                                        <?= match ($user['user_type'] ?? 'user') {
                                            'owner' => 'مالک',
                                            'tenant' => 'مستاجر',
                                            'manager' => 'مدیر',
                                            default => 'کاربر'
                                        } ?>
                                    </span>
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <a href="profile.php" class="dropdown-item <?= $current_page === 'profile.php' ? 'active' : '' ?>">
                                <i class="fas fa-user"></i>
                                پروفایل کاربری
                            </a>
                            <a href="settings.php" class="dropdown-item <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                                <i class="fas fa-cog"></i>
                                تنظیمات سیستم
                            </a>

                            <div class="dropdown-divider"></div>

                            <a href="logout.php" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                خروج از سیستم
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Enhanced Mobile Search Overlay -->
    <div class="mobile-search-overlay glass" id="mobileSearchOverlay">
        <div class="mobile-search-container">
            <form action="search.php" method="GET" class="mobile-search-form">
                <input type="text" name="q" placeholder="چه چیزی را جستجو می‌کنید؟" class="mobile-search-input glass" autofocus>
                <button type="submit" class="mobile-search-btn" aria-label="جستجو">
                    <i class="fas fa-search"></i>
                </button>
                <button type="button" class="mobile-search-close" id="mobileSearchClose" aria-label="بستن جستجو">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="js/theme.js"></script>
    <script src="js/app.js"></script>
</body>

</html>