<?php
// session_start();
require '../src/function.class.php';
require '../src/Database.class.php';

// $fn->nonAuthPage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .animated-background {
            background: linear-gradient(135deg, #4b6cb7, #182848);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(10px);
        }
        .shape-1 { width: 400px; height: 400px; top: -200px; left: -200px; }
        .shape-2 { width: 300px; height: 300px; bottom: -150px; right: -150px; }
        .shape-3 { width: 250px; height: 250px; top: 50%; left: 20%; }
        .card-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="animated-background flex items-center  justify-center text-white">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="container flex items-center justify-center h-screen max-w-7xl mx-auto px-4">
        <div class="flex w-full">
            <!-- Left Section -->
            <div class="w-1/2 p-8 flex flex-col justify-center">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16  flex items-center justify-center">
                            <img src="logo.png" alt="Logo" class="w-16 h-16 object-contain shadow-xl">
                        </div>
                    </div>
                    <h1 class="text-5xl font-bold mb-4">WELCOME<br>Resume Builder</h1>
                </div>
            </div>
            <!-- Right Section (Change Password Form) -->
            <div class="w-1/2 p-8 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg card-lift w-full max-w-md text-gray-800">
                    <form method="POST" action="changepassword.action.php">
                        <h2 class="text-2xl font-semibold mb-6 text-center">Change Password</h2>
                        <div class="mb-6">
                            <input type="password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" name="password" placeholder="Enter New Password" required>
                        </div>
                        <!-- <div class="mb-4">
                            <input type="password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Re Enter New Password" required>
                        </div> -->
                        
                        <div class=" mb-6">
                            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">Change Password</button>
                        </div>
                        <div class="text-center">
                            <div class="flex justify-center space-x-4 mt-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-blue-400 hover:text-blue-600"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-pink-600 hover:text-pink-800"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        <?php
        $fn->error();
        $fn->alert();
        ?>
    </script>
</body>
</html>