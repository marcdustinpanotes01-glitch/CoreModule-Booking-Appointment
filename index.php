<?php
    session_start();
    include_once("connection/connection.php");
    $con = connection();
    
    // Debug session (remove this later)
    echo "<!-- Session Debug: ";
    print_r($_SESSION);
    echo " -->";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #2c3e50;
        }

        body {
            line-height: 1.6;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--dark-color);
        }

        .logo img {
            height: 40px;
        }

        .logo span {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .login-btn {
            background: var(--primary-color);
            color: white !important;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
        }

        .login-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url('https://source.unsplash.com/1600x900/?technology,service') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 1rem;
            margin-top: 0;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .cta-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cta-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Sections */
        section {
            padding: 5rem 5%;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }

        /* About Section */
        .about {
            background: var(--light-color);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Gallery Section */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            height: 250px;
            overflow: hidden;
            border-radius: 10px;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Contact Section */
        .contact {
            background: var(--light-color);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .contact-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .contact-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: var(--dark-color);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-icon {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary-color);
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .user-icon:hover {
            background: var(--light-color);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1001;
        }

        .dropdown-content a {
            color: var(--dark-color);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-content a i {
            width: 20px;
        }

        .dropdown-content a:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .show {
            display: block;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                padding: 1rem;
                flex-direction: column;
            }

            .nav-links.active {
                display: flex;
            }

            .hero h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="#" class="logo">
            <img src="https://via.placeholder.com/40" alt="TechCare Logo">
            <span>ENNO</span>
        </a>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#gallery">Gallery</a>
            <a href="#contact">Contact</a>
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-dropdown">
                <i class="fas fa-user-circle user-icon" onclick="toggleDropdown()"></i>
                <div class="dropdown-content" id="userDropdown">
                    <a href="my-account.php"><i class="fas fa-user"></i> My Account</a>
                    <a href="my-appointments.php"><i class="fas fa-calendar-alt"></i> My Appointments</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            <?php else: ?>
            <a href="login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Welcome to Enno's Incubator</h1>
            <p>Your trusted partner for all technical services and solutions. We provide expert care for your devices.</p>
            <a href="#" onclick="checkLoginAndRedirect()" class="cta-btn">Book an Appointment</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <i class="fas fa-tools"></i>
                <h3>Calibration</h3>
                <p>Professional calibration services for your technical equipment with guaranteed accuracy.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-egg"></i>
                <h3>Egg Hatching</h3>
                <p>State-of-the-art egg hatching facilities with precise temperature and humidity control.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-wrench"></i>
                <h3>Repair Services</h3>
                <p>Expert repair services for all types of technical equipment and devices.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery">
        <h2 class="section-title">Our Gallery</h2>
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="https://source.unsplash.com/600x400/?technology,calibration" alt="Calibration">
            </div>
            <div class="gallery-item">
                <img src="https://source.unsplash.com/600x400/?eggs,incubator" alt="Egg Hatching">
            </div>
            <div class="gallery-item">
                <img src="https://source.unsplash.com/600x400/?repair,tools" alt="Repair">
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Our Location</h3>
                <p>123 Tech Street, Digital City</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Phone Number</h3>
                <p>+1 234 567 890</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email Address</h3>
                <p>contact@techcare.com</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 TechCare Solutions. All rights reserved.</p>
    </footer>

    <script>
        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Function to check login status and redirect accordingly
        function checkLoginAndRedirect() {
            <?php if(isset($_SESSION['user_id'])): ?>
                window.location.href = 'booking.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }

        // User dropdown toggle
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-icon')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
