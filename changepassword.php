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
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="bg-[url('https://img.freepik.com/free-photo/blue-toned-pack-paper-sheets-with-copy-space_23-2148320442.jpg?t=st=1743925575~exp=1743929175~hmac=454505f420a8086c800cc2543a06eb6272cc850da49eea362eebd824e57ba727&w=1380')] bg-cover flex items-center justify-center min-h-screen" >
    <div class=" container flex flex-col items-center justify-center mx-auto    p-6">
        <div class="bg-white shadow-md rounded-md p-6 min-w-80">
            <form action="changepassword.action.php" method="post">
                <div class="bg-white  rounded-md p-6 flex items-center space-x-2 justify-center">
                    <img src="logo.png" class="h-12 w-12">
                    <div>
                        <h1 class="text-2xl font-bold">CV Maker</h1>
                        <p>Change Password</p>
                    </div>
                </div>
                <div >
                    <label class="block text-gray-700"></label>
                    <input type="password" name="password" class="mt-1 block w-full p-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" placeholder=" Enter New Password" required>
                </div >
                <!-- <div >
                    <label class="block text-gray-700"></label>
                    <input type="password" class="mt-1 block w-full p-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" placeholder=" Re Enter New Password" required>
                </div > -->

                <button type="submit" class="mt-6 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-500 transition duration-200 ">Change Password</button>
                
                
                <div class="mt-4 flex justify-between text-sm text-blue-600">

                    <a href="" >Register</a>
                    <a href="" >Login</a>

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