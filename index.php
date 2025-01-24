<?php
session_start();
require_once 'config.php';

// Fetch categories and services
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group services by category
$servicesByCategory = [];
foreach ($services as $service) {
    $categoryId = $service['category_id'];
    if (!isset($servicesByCategory[$categoryId])) {
        $servicesByCategory[$categoryId] = [];
    }
    $servicesByCategory[$categoryId][] = $service;
}

// Fetch special packages
$specialPackages = array_filter($services, function($service) {
    return strpos(strtolower($service['name']), 'paket') !== false;
});
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larissa Salon Studio - Salon Kecantikan & Make Up Artist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navigation */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }
        .logo {
            height: 40px;
        }
        .nav-links a {
            color: #333;
            text-decoration: none;
            margin-left: 1rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #b69159;
        }

        /* Hero section */
        .hero {
            background-image: url('hero-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }
        .hero-content {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 2rem;
            border-radius: 8px;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        /* Services section */
        .services, .packages, .service-details {
            padding: 4rem 0;
        }
        .services h2, .packages h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .services-grid, .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        .service-card, .package-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        .service-card:hover, .package-card:hover {
            transform: translateY(-5px);
        }
        .service-card h3, .package-card h3 {
            margin-bottom: 1rem;
        }
        .service-card ul {
            list-style-type: none;
            margin-bottom: 1rem;
        }
        .service-card li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #b69159;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #a87e4e;
        }

        /* Service details */
        .service-details {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .service-details h2 {
            margin-bottom: 1rem;
        }
        .service-details p {
            margin-bottom: 1rem;
        }
        .service-details .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #b69159;
        }
        .service-details .duration {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }

        /* Footer */
        .footer {
            background-color: #333;
            color: #fff;
            padding: 2rem 0;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        .footer-section h3 {
            margin-bottom: 1rem;
        }
        .footer-section ul {
            list-style-type: none;
        }
        .footer-section li {
            margin-bottom: 0.5rem;
        }
        .social-icons a {
            color: #fff;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #555;
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .fade-in.active {
            opacity: 1;
        }
        .slide-in {
            transform: translateY(50px);
            opacity: 0;
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }
        .slide-in.active {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <img src="logo.png" alt="Larissa Salon Studio" class="logo">
                <div class="nav-links">
                    <a href="#home">Home</a>
                    <a href="#services">Services</a>
                    <a href="#packages">Packages</a>
                    <a href="#contact">Contact Us</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php">My Account</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="hero-content" data-aos="fade-up">
            <h1>Larissa Salon Studio</h1>
            <p>Salon Kecantikan & Make Up Artist (MUA) di Kota Binjai</p>
            <p>Buka Setiap Hari: 10.00 - 19.00 WIB</p>
            <a href="#services" class="btn">Explore Our Services</a>
        </div>
    </section>

    <section class="services" id="services">
        <div class="container">
            <h2 data-aos="fade-up">Our Services</h2>
            <div class="services-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="service-card" data-aos="fade-up">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <ul>
                            <?php if (isset($servicesByCategory[$category['id']])): ?>
                                <?php foreach (array_slice($servicesByCategory[$category['id']], 0, 3) as $service): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($service['name']); ?></span>
                                        <span>Rp <?php echo number_format($service['price'], 0, ',', '.'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <a href="#" class="btn view-services" data-category="<?php echo $category['id']; ?>">View All Services</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="packages" id="packages">
        <div class="container">
            <h2 data-aos="fade-up">Special Packages</h2>
            <div class="package-grid">
                <?php foreach ($specialPackages as $package): ?>
                    <div class="package-card" data-aos="fade-up">
                        <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                        <p><?php echo htmlspecialchars($package['description']); ?></p>
                        <p><strong>Price: Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></strong></p>
                        <a href="#" class="btn view-service" data-service="<?php echo $package['id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="service-details" id="service-details" style="display: none;">
        <div class="container">
            <div id="service-details-content"></div>
        </div>
    </section>

    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <img src="logo.png" alt="Larissa Salon Studio" class="logo">
                    <p>"Kecantikan Anda adalah Prioritas Kami"</p>
                    <div class="social-icons">
                        <a href="https://instagram.com/larissasalonstudio" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6282268777018" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>Instagram: @larissasalonstudio</li>
                        <li>WhatsApp: +6282268777018</li>
                        <li>Email: makeuprisa3@gmail.com</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#packages">Packages</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Larissa Salon Studio. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        $(document).ready(function() {
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // View all services for a category
            $('.view-services').on('click', function(e) {
                e.preventDefault();
                var categoryId = $(this).data('category');
                $.ajax({
                    url: 'get_services.php',
                    method: 'GET',
                    data: { category_id: categoryId },
                    success: function(response) {
                        $('#service-details-content').html(response);
                        $('#service-details').fadeIn();
                        $('html, body').animate({
                            scrollTop: $('#service-details').offset().top - 100
                        }, 1000);
                    }
                });
            });

            // View service details
            $('.view-service').on('click', function(e) {
                e.preventDefault();
                var serviceId = $(this).data('service');
                $.ajax({
                    url: 'get_service_details.php',
                    method: 'GET',
                    data: { service_id: serviceId },
                    success: function(response) {
                        $('#service-details-content').html(response);
                        $('#service-details').fadeIn();
                        $('html, body').animate({
                            scrollTop: $('#service-details').offset().top - 100
                        }, 1000);
                    }
                });
            });
        });
    </script>
</body>
</html>

