<footer class="main-footer glass">
    <div class="footer-container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <h3 class="footer-title">
                    <i class="fas fa-chart-line"></i>
                    سیستم مدیریت مالی
                </h3>
                <p class="footer-description">
                    سیستم مدیریت هوشمند مالی و حسابداری با قابلیت‌های پیشرفته برای مدیریت کامل تراکنش‌ها،
                    گزارش‌گیری و آنالیز مالی در ساختمان‌های مسکونی و اداری.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h3 class="footer-title">لینک‌های سریع</h3>
                <ul class="footer-links">
                    <li><a href="home.php"><i class="fas fa-home"></i> داشبورد</a></li>
                    <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> تراکنش‌ها</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> گزارشات</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> پیام‌ها</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> تنظیمات</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="footer-section">
                <h3 class="footer-title">پشتیبانی</h3>
                <ul class="footer-links">
                    <li><a href="help.php"><i class="fas fa-question-circle"></i> راهنمایی</a></li>
                    <li><a href="contact.php"><i class="fas fa-headset"></i> تماس با پشتیبانی</a></li>
                    <li><a href="faq.php"><i class="fas fa-comments"></i> سوالات متداول</a></li>
                    <li><a href="documentation.php"><i class="fas fa-book"></i> مستندات</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h3 class="footer-title">تماس با ما</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>09059044538</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>gnmhdi@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>تهران، خیابان آزادی، پلاک ۱۲۳</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>شنبه تا چهارشنبه: ۸:۰۰ - ۱۷:۰۰</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?= date('Y') ?> سیستم مدیریت مالی. تمام حقوق محفوظ است.</p>
                </div>

                <div class="footer-links-bottom">
                    <a href="privacy.php">حریم خصوصی</a>
                    <a href="terms.php">قوانین و مقررات</a>
                    <a href="sitemap.php">نقشه سایت</a>
                </div>

                <div class="footer-info">
                    <div class="current-time">
                        <i class="far fa-clock"></i>
                        <span id="currentDateTime"><?= jdate('l، d F Y H:i') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="js/footer.js"></script>