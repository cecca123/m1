</main>
        
        <!-- Footer -->
        <footer class="app-footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo">
                        <img src="<?= APP_URL ?>/assets/images/logo.svg" alt="<?= APP_NAME ?>">
                        <p><?= APP_NAME ?></p>
                    </div>
                    
                    <div class="footer-links">
                        <div class="footer-column">
                            <h3>Quick Links</h3>
                            <ul>
                                <li><a href="<?= APP_URL ?>">Home</a></li>
                                <li><a href="<?= APP_URL ?>/pages/stations.php">Stations</a></li>
                                <?php if (isLoggedIn()): ?>
                                    <li><a href="<?= APP_URL ?>/pages/bookings.php">Bookings</a></li>
                                    <li><a href="<?= APP_URL ?>/pages/dashboard.php">Dashboard</a></li>
                                <?php else: ?>
                                    <li><a href="<?= APP_URL ?>/pages/login.php">Login</a></li>
                                    <li><a href="<?= APP_URL ?>/pages/register.php">Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="footer-column">
                            <h3>Support</h3>
                            <ul>
                                <li><a href="#">Help Center</a></li>
                                <li><a href="#">Contact Us</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">Terms of Service</a></li>
                            </ul>
                        </div>
                        
                        <div class="footer-column">
                            <h3>Follow Us</h3>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- JavaScript -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
    <?php if (isset($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= APP_URL ?>/assets/js/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Close flash message
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                const closeButton = flashMessage.querySelector('.close-flash');
                closeButton.addEventListener('click', function() {
                    flashMessage.style.display = 'none';
                });
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    flashMessage.style.display = 'none';
                }, 5000);
            }
            
            // Mobile menu toggle
            const menuToggle = document.getElementById('menuToggle');
            const mainNav = document.querySelector('.main-nav');
            
            if (menuToggle && mainNav) {
                menuToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>