<?php
// session_start();
require '../src/function.class.php';
$fn->nonAuthPage(); // Assuming this prevents access if already logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .animate-slide-up {
            animation: slideUp 1s ease-out;
        }
        .animate-fade-in {
            animation: fadeIn 1.5s ease-in-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animated-background {
            background: linear-gradient(135deg, #4b6cb7, #182848);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
            position: relative;
            min-height: 100vh;
            /* overflow: hidden; */
        }
        @keyframes gradientShift {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            filter: blur(15px);
        }
        .shape-1 { width: 400px; height: 400px; top: 0; left: 0; }
        .shape-2 { width: 300px; height: 300px; bottom: 0; right: 0; }
        .shape-3 { width: 250px; height: 250px; top: 30%; left: 15%; }
        .text-white, .text-gray-800, .text-gray-600 {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        /* a, button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        } */
        a:hover, button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
            color: #e0e0e0 !important;
        }
        header {
            background: linear-gradient(135deg, #4b6cb7, #182848);
        }
        footer {
            background: linear-gradient(135deg, #2a4068, #0a1a38);
        }
        .shadow-2xl, .shadow-md, .shadow-lg {
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hover:shadow-xl {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="animated-background  min-h-screen">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <!-- Sticky Header -->
    <header class="bg-gradient-to-r from-[#4b6cb7] to-[#182848] text-white py-3 fixed top-0 w-full z-10 shadow-lg">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="logo.png" alt="Logo" class="h-12 w-12 animate-spin-slow">
                <h1 class="text-3xl font-bold text-white">Resume Builder</h1>
            </div>
            <nav class="space-x-4">
                <a href="welcome.php" class="hover:underline hover:text-blue-200 transition duration-200">Home</a>
                <a href="login.php" class="hover:underline hover:text-blue-200 transition duration-200">Login</a>
                <a href="register.php" class="hover:underline hover:text-blue-200 transition duration-200">Register</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto mt-20 px-4 py-12">
        <h1 class="text-4xl font-bold text-white text-center mb-10 animate-slide-up">About Us</h1>

        <!-- Mission Section -->
        <section class="bg-white bg-opacity-80 rounded-xl p-8 mb-12 shadow-2xl hover:shadow-xl transition duration-300">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 animate-fade-in" style="animation-delay: 0.2s;">Our Mission</h2>
            <p class="text-gray-600 leading-relaxed">
                At Resume Builder, we are dedicated to empowering individuals to create professional resumes with ease. Our mission is to provide user-friendly tools and customizable templates that help you stand out in the job market. Founded with a passion for career development, we aim to simplify the resume-building process for everyone, from fresh graduates to seasoned professionals.
            </p>
        </section>

        <!-- Team Section -->
        <section class="bg-white bg-opacity-80 rounded-xl p-8 mb-12 shadow-2xl hover:shadow-xl transition duration-300">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 animate-fade-in" style="animation-delay: 0.4s;">Our Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    
                    <h3 class="text-lg font-semibold text-gray-800">Rahul Ranjan</h3>
                    <p class="text-gray-600">12303292</p>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-800">Akshat Kumar</h3>
                    <p class="text-gray-600">12313940</p>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-800">Sahil Sindhu</h3>
                    <p class="text-gray-600">12315193</p>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-800">Anuj Malik</h3>
                    <p class="text-gray-600">12312195</p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="bg-white bg-opacity-80 rounded-xl p-8 shadow-2xl hover:shadow-xl transition duration-300">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 animate-fade-in" style="animation-delay: 0.6s;">Contact Us</h2>
            <p class="text-gray-600 mb-4">
                Have questions or feedback? Reach out to us!
            </p>
            <div class="space-y-4">
                <p class="text-gray-600"><i class="fas fa-envelope mr-2"></i> Email: rahul.hts21@gmail.com</p>
                <p class="text-gray-600"><i class="fas fa-phone mr-2"></i> Phone: +91 7909087007</p>
                <a href="mailto: rahul.hts21@gmail.com" class="inline-block mt-4 px-6 py-2 rounded-full hover:bg-blue-700 transition duration-300">
                    <i class="fas fa-paper-plane mr-2"></i> Send Us a Message
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-[#2a4068] to-[#0a1a38] text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>© <?php echo date('Y'); ?> Resume Builder. All rights reserved.</p>
            <div class="mt-2 space-x-4">
                <a href="welcome.php" class="hover:underline hover:text-blue-300 transition duration-200">Home</a>
                <a href="aboutus.php" class="hover:underline hover:text-blue-300 transition duration-200">About Us</a>
            </div>
        </div>
    </footer>

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