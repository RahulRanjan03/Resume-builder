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
    <title>Welcome | Resume Builder</title>
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
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Sticky Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 fixed top-0 w-full z-10 shadow-lg">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="logo.png" alt="Logo" class="h-12 w-12 animate-spin-slow">
                <h1 class="text-3xl font-bold">Resume Builder</h1>
            </div>
            <nav class="space-x-4">
                <a href="login.php" class="hover:underline hover:text-blue-200 transition duration-200">Login</a>
                <a href="register.php" class="hover:underline hover:text-blue-200 transition duration-200">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section with Animation -->
    <section class="bg-[url('https://img.freepik.com/free-photo/top-view-desk-concept-with-gray-background_23-2148236825.jpg?t=st=1744173605~exp=1744177205~hmac=6b5feadff806f3badd9f00daf2a0b7636b885231525a0cc137915dbabbbe4cce&w=1380')] bg-cover bg-center py-24 text-center mt-16">
        <div class="container mx-auto px-4">
            <h2 class="text-5xl font-bold text-white drop-shadow-lg mb-4 animate-slide-up">Create Your Perfect Resume</h2>
            <p class="text-xl text-white drop-shadow-lg mb-8 animate-slide-up" style="animation-delay: 0.2s;">Build professional resumes with ease using our customizable templates.</p>
            <div class="space-x-4 animate-fade-in" style="animation-delay: 0.4s;">
                <a href="login.php" class="inline-block bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-500 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <a href="register.php" class="inline-block bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-500 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-b from-gray-100 to-gray-200">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">Why Choose Us?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <i class="fas fa-edit text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800">Easy Customization</h4>
                    <p class="text-gray-600 mt-2">Edit your resume with a user-friendly interface.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <i class="fas fa-file-download text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800">Multiple Formats</h4>
                    <p class="text-gray-600 mt-2">Download in PDF, Word, or other formats.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <i class="fas fa-rocket text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800">Fast & Efficient</h4>
                    <p class="text-gray-600 mt-2">Create a resume in minutes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Templates Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">Explore Our Templates</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 ">
                <!-- Template 1 -->
                <div class="bg-gray-100 rounded-lg shadow-2xl overflow-hidden hover:shadow-xl transition duration-300 transform hover:scale-105">
                    <img src="tem1.png" alt="Template 1" class="w-full h-48 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-xl font-semibold text-gray-800">Classic Resume</h4>
                        <p class="text-gray-600">A timeless design for any industry.</p>
                    </div>
                </div>
                <!-- Template 2 -->
                <div class="bg-gray-100 rounded-lg shadow-2xl overflow-hidden hover:shadow-xl transition duration-300 transform hover:scale-105">
                    <img src="tem2.png" alt="Template 2" class="w-full h-48 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-xl font-semibold text-gray-800">Modern Resume</h4>
                        <p class="text-gray-600">Sleek and stylish for creative roles.</p>
                    </div>
                </div>
                <!-- Template 3 -->
                <div class="bg-gray-100 rounded-lg shadow-2xl overflow-hidden hover:shadow-xl transition duration-300 transform hover:scale-105">
                    <img src="tem3.png" alt="Template 3" class="w-full h-48 object-cover rounded-t-md">
                    <div class="p-4">
                        <h4 class="text-xl font-semibold text-gray-800">Professional Resume</h4>
                        <p class="text-gray-600">Perfect for corporate environments.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-8">
                <a href="#" class="text-blue-600 hover:underline hover:text-blue-800 transition duration-200">See More Templates</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">What Our Users Say</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <p class="text-gray-600 italic">"This tool made creating my resume so simple and fast!"</p>
                    <p class="mt-4 text-gray-800 font-semibold">– Sarah K.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <p class="text-gray-600 italic">"The templates are professional and easy to customize."</p>
                    <p class="mt-4 text-gray-800 font-semibold">– John D.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p>© <?php echo date('Y'); ?> Resume Builder. All rights reserved.</p>
            <div class="mt-2 space-x-4">
                <a href="#" class="hover:underline hover:text-blue-300 transition duration-200">About Us</a>
                <a href="#" class="hover:underline hover:text-blue-300 transition duration-200">Contact</a>
                <a href="#" class="hover:underline hover:text-blue-300 transition duration-200">Privacy Policy</a>
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