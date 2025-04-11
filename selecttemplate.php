<?php
require 'function.class.php';
require '../src/database.class.php';


$fn->AuthPage();
$userId = $_SESSION['user_id'] ?? 0;

$resumeId = $_GET['id'] ?? null;
if (!$resumeId) {
    header("Location: myresumes.php");
    exit();
}

// Verify resume exists
$stmt = $db->prepare("SELECT id FROM resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resumeId, $userId);
$stmt->execute();
$result = $stmt->get_result();
if (!$result->fetch_assoc()) {
    header("Location: myresumes.php");
    exit();
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template'])) {
    $templateId = intval($_POST['template']);
    $stmt = $db->prepare("UPDATE resumes SET template = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $templateId, $resumeId, $userId);
    $stmt->execute();
    $stmt->close();
    
    header("Location: viewresume.php?id=$resumeId");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Resume Template | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[url('https://img.freepik.com/free-photo/blue-toned-pack-paper-sheets-with-copy-space_23-2148320442.jpg?t=st=1743925575~exp=1743929175~hmac=454505f420a8086c800cc2543a06eb6272cc850da49eea362eebd824e57ba727&w=1380')] bg-cover font-['Poppins'] min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white bg-opacity-95 h-16 px-6 py-4 flex justify-between items-center shadow-xl sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <img src="logo.png" class="h-8 w-8" alt="Logo">
            <h1 class="text-2xl font-bold text-gray-800">Resume Builder</h1>
        </div>
        <div class="flex space-x-4">
            <!-- <button class="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-800 transition duration-300">Profile</button> -->
            <a href="logout.actions.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Choose Your Resume Template</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <!-- Template 1 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="tem1.png" alt="Template 1" class="w-full h-48 object-cover rounded-md">
                    <h2 class="text-xl font-semibold text-gray-800 mt-4">Modern Resume</h2>
                    <p class="text-gray-600 text-sm mt-1">A clean, modern design with highlights on skills and experience.</p>
                    <input type="hidden" name="template" value="1">
                    <button type="submit" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 2 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="tem2.png" alt="Template 2" class="w-full h-48 object-cover rounded-md">
                    <h2 class="text-xl font-semibold text-gray-800 mt-4">Classic Resume</h2>
                    <p class="text-gray-600 text-sm mt-1">A professional and traditional layout for a polished look.</p>
                    <input type="hidden" name="template" value="2">
                    <button type="submit" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 3 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="tem3.png" alt="Template 3" class="w-full h-48 object-cover rounded-md">
                    <h2 class="text-xl font-semibold text-gray-800 mt-4">Creative Resume</h2>
                    <p class="text-gray-600 text-sm mt-1">An artistic and unique design for creative professionals.</p>
                    <input type="hidden" name="template" value="3">
                    <button type="submit" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 4 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="resume4.jpg" alt="Template 4" class="w-full h-48 object-cover rounded-md">
                    <h2 class="text-xl font-semibold text-gray-800 mt-4">Professional Resume</h2>
                    <p class="text-gray-600 text-sm mt-1">A sleek, professional design with a focus on achievements and education.</p>
                    <input type="hidden" name="template" value="4">
                    <button type="submit" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>