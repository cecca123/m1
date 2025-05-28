<?php
// Set page title
$pageTitle = 'Home';

// Include header
require_once 'includes/header.php';
?>

    <div class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Charge Your Electric Vehicle with Ease</h1>
                <p>Find, book, and use charging stations in your area. Fast, reliable, and convenient.</p>
                <div class="hero-cta">
                    <a href="<?= APP_URL ?>/pages/stations.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-charging-station"></i> Find Stations
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/pages/register.php" class="btn btn-outline btn-lg">
                            <i class="fas fa-user-plus"></i> Register Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="rounded-iframe-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d27116.334422518736!2d8.93859901112898!3d44.407442547269085!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sit!2sit!4v1747332788287!5m2!1sit!2sit" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <div class="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Our Charging Network</h2>
                <p>We provide everything you need for a seamless charging experience</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3>Easy Station Locator</h3>
                    <p>Find charging stations near you with real-time availability status.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Convenient Booking</h3>
                    <p>Reserve your charging slot in advance to ensure availability.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Fast Charging</h3>
                    <p>Multiple connector types and power outputs for different vehicle needs.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Track Your Usage</h3>
                    <p>Monitor your charging history and expenses with detailed analytics.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="availability-checker bg-light">
        <div class="container">
            <div class="section-header">
                <h2>Check Station Availability</h2>
                <p>Find available charging stations for your desired time</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="availability-checker-form" class="form-inline">
                        <div class="form-group">
                            <label for="check-date">Date</label>
                            <input type="date" id="check-date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                            <label for="check-time">Time</label>
                            <input type="time" id="check-time" name="time" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Check Availability
                        </button>
                    </form>

                    <div id="availability-results" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Charging your vehicle with us is simple and hassle-free</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Create an Account</h3>
                        <p>Register on our platform to access all charging stations and features.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Find & Book a Station</h3>
                        <p>Locate an available charging station and reserve your time slot.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Charge Your Vehicle</h3>
                        <p>Arrive at the station, plug in your vehicle, and start charging.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Pay & Track</h3>
                        <p>Monitor your charging progress and view your usage history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="testimonials bg-light">
        <div class="container">
            <div class="section-header">
                <h2>What Our Users Say</h2>
                <p>Hear from drivers who use our charging network</p>
            </div>

            <div class="testimonials-carousel">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The booking system is fantastic! I love being able to reserve a charging slot in advance. It saves me so much time."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Sarah L.">
                        <div>
                            <h4>Sarah L.</h4>
                            <p>Tesla Model 3 Owner</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The real-time station status feature is a game changer. I can always see which stations are available before I leave home."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael T.">
                        <div>
                            <h4>Michael T.</h4>
                            <p>Nissan Leaf Owner</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"I love how easy it is to track my charging history and expenses. The dashboard provides all the information I need."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emma R.">
                        <div>
                            <h4>Emma R.</h4>
                            <p>Hyundai Kona Electric Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Charging?</h2>
                <p>Join our network of EV drivers and enjoy convenient charging services.</p>
                <div class="cta-buttons">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>/pages/stations.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-map-marker-alt"></i> Find Stations
                        </a>
                        <a href="<?= APP_URL ?>/pages/dashboard.php" class="btn btn-outline btn-lg">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/pages/register.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus"></i> Create an Account
                        </a>
                        <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-outline btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hero Section */
        .hero {
            padding: var(--space-8) 0;
            overflow: hidden;
            text-align: center;
        }

        .hero .container {
            display: flex;
            align-items: center;
            gap: var(--space-8);
        }

        .hero-content {
            flex: 1;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--space-4);
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: var(--gray-700);
            margin-bottom: var(--space-6);
            line-height: 1.5;
        }

        .hero-cta {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            transition: transform var(--transition);
        }

        .hero-image img:hover {
            transform: scale(1.02);
        }

        /* Features Section */
        .features {
            padding: var(--space-16) 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--space-12);
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--gray-600);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--space-8);
        }

        .feature-card {
            background-color: var(--white);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-light);
            color: var(--white);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-4);
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-3);
        }

        .feature-card p {
            color: var(--gray-600);
        }

        /* Availability Checker */
        .availability-checker {
            padding: var(--space-16) 0;
        }

        /* How It Works */
        .how-it-works {
            padding: var(--space-16) 0;
        }

        .steps {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 30px;
            width: 2px;
            background-color: var(--primary-light);
        }

        .step {
            display: flex;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
            position: relative;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background-color: var(--primary);
            color: var(--white);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            z-index: 1;
        }

        .step-content {
            flex: 1;
            padding-top: var(--space-2);
        }

        .step-content h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }

        .step-content p {
            color: var(--gray-600);
        }

        /* Testimonials */
        .testimonials {
            padding: var(--space-16) 0;
        }

        .testimonials-carousel {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--space-6);
        }

        .testimonial-card {
            background-color: var(--white);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            box-shadow: var(--shadow);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .testimonial-content {
            margin-bottom: var(--space-4);
        }

        .testimonial-content p {
            font-style: italic;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-full);
            object-fit: cover;
        }

        .testimonial-author h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0;
        }

        .testimonial-author p {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin: 0;
        }

        /* CTA Section */
        .cta-section {
            padding: var(--space-16) 0;
            background-color: var(--primary);
            color: var(--white);
            text-align: center;
        }

        .cta-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-3);
        }

        .cta-content p {
            font-size: 1.1rem;
            margin-bottom: var(--space-6);
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: var(--space-4);
        }

        .cta-buttons .btn-outline {
            background-color: transparent;
            border-color: var(--white);
            color: var(--white);
        }

        .cta-buttons .btn-outline:hover {
            background-color: var(--white);
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero .container {
                flex-direction: column;
                text-align: center;
            }

            .hero-cta {
                justify-content: center;
            }

            .hero-image {
                justify-content: center;
                margin-top: var(--space-6);
            }

            .steps::before {
                left: 25px;
            }

            .step-number {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }

            .step {
                gap: var(--space-4);
            }
        }

        @media (max-width: 768px) {
            .cta-buttons {
                flex-direction: column;
                gap: var(--space-3);
            }

            .features-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .testimonials-carousel {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-cta {
                flex-direction: column;
                gap: var(--space-3);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .section-header h2 {
                font-size: 1.75rem;
            }
        }

        /* ----------------- iFrame ----------------- */
        .rounded-iframe-container {
            display: block;
            width: 75vw;
            max-width: 100%;
            border-radius: 20px;
            overflow: hidden;
            margin: 25px auto;
            height: 60vh;
        }

        .rounded-iframe-container iframe {
            width: 100%;
            height: 100%;
            display: block;
            border: none;
        }
    </style>

    <!-- Include bookings.js for availability checker -->
    <script src="<?= APP_URL ?>/assets/js/bookings.js"></script>

<?php
// Include footer
require_once 'includes/footer.php';
?>