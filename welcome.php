<?php
// session_start();
require '../src/function.class.php';
$fn->nonAuthPage(); // Prevents access if already logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            color: #ffffff;
            overflow-x: hidden;
            min-height: 100vh;
        }
        .navbar {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .nav-circle {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .nav-circle:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }
        .nav-menu {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .nav-circle.active .nav-menu {
            opacity: 1;
            visibility: visible;
        }
        .nav-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #1e3a8a;
            font-weight: 500;
        }
        .nav-menu a:hover {
            background: #e0e7ff;
        }
        .hero {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: pulse 5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1.5s ease-out;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #ffd700;
            color: #1e3a8a;
            margin-right: 1rem;
        }
        .btn-primary:hover {
            background: #ffca28;
            transform: translateY(-5px);
        }
        .btn-secondary {
            background: #ffffff;
            color: #ff6f61;
        }
        .btn-secondary:hover {
            background: #f3f4f6;
            transform: translateY(-5px);
        }
        .features {
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        .swiper {
            width: 100%;
            padding: 2rem 0;
        }
        .swiper-slide {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            color: #1e3a8a;
            transition: transform 0.3s;
        }
        .swiper-slide:hover {
            transform: scale(1.05);
        }
        .templates {
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        .template-card {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        .template-card:hover {
            transform: translateY(-10px) rotate(2deg);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .template-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .testimonials {
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        .testimonial-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            color: #1e3a8a;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            transition: box-shadow 0.3s;
        }
        .testimonial-card:hover {
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
        }
        .footer {
            padding: 2rem;
            background: #1e3a8a;
            clip-path: polygon(0 0, 100% 10%, 100% 100%, 0 90%);
            text-align: center;
        }
        .footer a {
            color: #ffd700;
            margin: 0 1rem;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #ffca28;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.2rem;
            }
            .btn-secondary {
                margin-top: 1rem;
                margin-left: 0;
            }
            .swiper-slide, .template-card, .testimonial-card {
                margin-bottom: 1.5rem;
            }
        }
        .shape-1 { width: 400px; height: 400px; top: 0; left: 0; }
        .shape-2 { width: 300px; height: 300px; bottom: 0; right: 0; }
        .shape-3 { width: 250px; height: 250px; top: 50%; left: 20%; }
    </style>
</head>
<body>
<header class="bg-gradient-to-r from-[#4b6cb7] to-[#182848] text-white py-3 fixed top-0 w-full z-10 shadow-lg">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="logo.png" alt="Logo" class="h-12 w-12 animate-spin-slow">
                <h1 class="text-3xl font-bold text-white">Resume Builder</h1>
            </div>
            <nav class="space-x-4">
                <a href="login.php" class="hover:underline hover:text-blue-200 transition duration-200">Login</a>
                <a href="register.php" class="hover:underline hover:text-blue-200 transition duration-200">Register</a>
            </nav>
        </div>
    </header>
    <!-- Navbar -->
    <nav class="navbar">
        
        <div class="nav-menu">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Build Your Dream Resume</h1>
            <p>Unleash your potential with stunning, customizable resume designs.</p>
            <div>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <i class="fas fa-edit text-4xl text-teal-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Easy Customization</h3>
                    <p class="text-gray-700 mt-2">Tailor your resume with a simple interface.</p>
                </div>
                <div class="swiper-slide">
                    <i class="fas fa-file-download text-4xl text-teal-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Multiple Formats</h3>
                    <p class="text-gray-700 mt-2">Export in PDF, Word, and more.</p>
                </div>
                <div class="swiper-slide">
                    <i class="fas fa-rocket text-4xl text-teal-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Fast Creation</h3>
                    <p class="text-gray-700 mt-2">Build your resume in minutes.</p>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Templates Section -->
    <section class="templates">
        <h2 class="text-3xl font-bold text-center mb-6">Discover Our Templates</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="template-card">
                <img src="tem1.png" alt="Classic Resume">
                <div class="p-4 text-center">
                    <h3 class="text-xl font-semibold text-gray-800">Classic Resume</h3>
                    <p class="text-gray-600">Timeless and professional.</p>
                </div>
            </div>
            <div class="template-card">
                <img src="tem2.png" alt="Modern Resume">
                <div class="p-4 text-center">
                    <h3 class="text-xl font-semibold text-gray-800">Modern Resume</h3>
                    <p class="text-gray-600">Trendy and creative.</p>
                </div>
            </div>
            <div class="template-card">
                <img src="tem3.png" alt="Professional Resume">
                <div class="p-4 text-center">
                    <h3 class="text-xl font-semibold text-gray-800">Professional Resume</h3>
                    <p class="text-gray-600">Ideal for corporate roles.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <!-- <a href="#" class="text-white hover:underline">View All Templates</a> -->
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2 class="text-3xl font-bold text-center mb-6">What Our Users Say</h2>
        <div class="swiper mySwiper2">
            <div class="swiper-wrapper">
                <div class="swiper-slide testimonial-card">
                    <p class="text-gray-700 italic">"Amazing tool! My resume was ready in no time!"</p>
                    <p class="mt-4 font-semibold">– Emily R.</p>
                </div>
                <div class="swiper-slide testimonial-card">
                    <p class="text-gray-700 italic">"The templates are top-notch and easy to use."</p>
                    <p class="mt-4 font-semibold">– Michael T.</p>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <footer class="bg-gradient-to-r from-[#2a4068] to-[#0a1a38] text-white py-6">
    <div class="container mx-auto px-4 text-center">
        <p>© <?php echo date('Y'); ?> Resume Builder. All rights reserved.</p>
        <div class="mt-2 space-x-4">
            <a href="welcome.php" class="hover:underline hover:text-blue-300 transition duration-200">Home</a>
            <a href="aboutus.php" class="hover:underline hover:text-blue-300 transition duration-200">About Us</a>
        </div>
    </div>
</footer>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        // Navbar Menu Toggle
        function toggleMenu() {
            const navCircle = document.querySelector('.nav-circle');
            navCircle.classList.toggle('active');
        }
        document.addEventListener('click', (e) => {
            const navCircle = document.querySelector('.nav-circle');
            if (!navCircle.contains(e.target)) {
                navCircle.classList.remove('active');
            }
        });

        // Swiper for Features
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            loop: true,
        });

        // Swiper for Testimonials
        var swiper2 = new Swiper(".mySwiper2", {
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
            },
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            loop: true,
        });
    </script>

    <!-- SweetAlert2 for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        $fn->error();
        $fn->alert();
        ?>
    </script>
</body>
</html>