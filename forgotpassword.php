<?php
// session_start();
require '../src/function.class.php';
// $fn->nonAuthPage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[url('https://img.freepik.com/free-photo/top-view-desk-concept-with-gray-background_23-2148236825.jpg?t=st=1744173605~exp=1744177205~hmac=6b5feadff806f3badd9f00daf2a0b7636b885231525a0cc137915dbabbbe4cce&w=1380')] bg-cover flex items-center justify-center min-h-screen">
    <div class="container flex flex-col items-center justify-center mx-auto p-8">
        <div class="bg-white shadow-lg p-8 w-full max-w-md rounded-lg">
            <form action="sendcode.action.php" method="post">
                <div class="bg-white p-6 flex items-center space-x-2 justify-center">
                    <img src="logo.png" class="h-12 w-12">
                    <div>
                        <h1 class="text-2xl"><b>Resume</b> Builder</h1>
                        <p>Forgot Your Password</p>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700"></label>
                    <input type="email" name="email_id" class="mt-1 block w-full p-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" placeholder="Email address" required>
                </div>
                <button type="submit" class="mt-6 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-500 transition duration-200 flex items-center justify-center">
                    <img src="png.png" class="h-5 w-5 mr-2">
                    <span>Send Verification Code</span>
                </button>
                <div class="mt-4 flex justify-between text-sm text-blue-600">
                    <a href="register.php">Register</a>
                    <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        $fn->error();
        $fn->alert();
        ?>
    </script>
</body>
</html>