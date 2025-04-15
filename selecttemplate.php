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
    <style>
        .animated-background {
            background: linear-gradient(135deg, #4b6cb7, #182848);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
            position: relative;
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
        .shape-1 { width: 400px; height: 400px; top: 0; left: 0; }
        .shape-2 { width: 300px; height: 300px; bottom: 0; right: 0; }
        .shape-3 { width: 250px; height: 250px; top: 50%; left: 20%; }
        .template-card {
            height: 380px; /* Fixed height for all cards */
            display: flex;
            flex-direction: column;
        }
        .template-text {
            height: 120px; /* Fixed height for title + description */
            overflow: hidden;
        }
    </style>
</head>
<body class="animated-background pb-8 min-h-screen">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <!-- Navbar -->
    <nav class="bg-white bg-opacity-95 h-16 px-6 py-4 flex justify-between items-center shadow-xl sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <img src="logo.png" class="h-8 w-8" alt="Logo">
            <h1 class="text-2xl font-bold text-gray-800">Resume Builder</h1>
        </div>
        <div class="flex space-x-4">
            <a href="logout.actions.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-white text-center mb-8">Choose Your Resume Template</h1>
        
        <!-- First Row: Templates 1–4 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto mb-6">
            <!-- Template 1 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem1.png" alt="Template 1" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Modern Resume</h2>
                        <p class="text-gray-600 text-sm mt-1">A clean, modern design with highlights on skills and experience.</p>
                    </div>
                    <input type="hidden" name="template" value="1">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 2 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem2.png" alt="Template 2" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Classic Resume</h2>
                        <p class="text-gray-600 text-sm mt-1">A professional and traditional layout for a polished look.</p>
                    </div>
                    <input type="hidden" name="template" value="2">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 3 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem3.png" alt="Template 3" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Creative Resume</h2>
                        <p class="text-gray-600 text-sm mt-1">An artistic and unique design for creative professionals.</p>
                    </div>
                    <input type="hidden" name="template" value="3">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 4 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="resume4.jpg" alt="Template 4" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Professional Resume</h2>
                        <p class="text-gray-600 text-sm mt-1">A sleek, professional design with a focus on achievements and education.</p>
                    </div>
                    <input type="hidden" name="template" value="4">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>
        </div>

        <!-- Second Row: Templates 5–8 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <!-- Template 5 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="temp5.png" alt="Template 5" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Tech Innovator</h2>
                        <p class="text-gray-600 text-sm mt-1">A dynamic layout for developers and tech professionals, emphasizing projects and skills.</p>
                    </div>
                    <input type="hidden" name="template" value="5">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 6 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem6.png" alt="Template 6" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Finance Ledger</h2>
                        <p class="text-gray-600 text-sm mt-1">A structured design for finance experts, highlighting precision and achievements.</p>
                    </div>
                    <input type="hidden" name="template" value="6">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 7 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem7.png" alt="Template 7" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Educator’s Notebook</h2>
                        <p class="text-gray-600 text-sm mt-1">A warm, organized layout for teachers, focusing on education and impact.</p>
                    </div>
                    <input type="hidden" name="template" value="7">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>

            <!-- Template 8 -->
            <form method="POST" action="">
                <div class="bg-white bg-opacity-90 shadow-2xl rounded-xl p-4 w-full max-w-xs mx-auto hover:shadow-lg hover:-translate-y-1 transition duration-300 template-card">
                    <img src="tem8.png" alt="Template 8" class="w-full h-48 object-cover rounded-md">
                    <div class="template-text">
                        <h2 class="text-xl font-semibold text-gray-800 mt-4">Hospitality Flow</h2>
                        <p class="text-gray-600 text-sm mt-1">A welcoming design for hospitality professionals, showcasing service and leadership.</p>
                    </div>
                    <input type="hidden" name="template" value="8">
                    <button type="submit" class="mt-auto w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Use This Template</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>