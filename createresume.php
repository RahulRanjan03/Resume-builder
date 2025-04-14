<?php
// [PHP section unchanged]
require 'function.class.php';
require '../src/database.class.php';

$fn->AuthPage();
$userId = $_SESSION['user_id'] ?? 0;

if (!$userId) {
    $fn->setError('Please log in to create a resume.');
    $fn->redirect('login.php');
    exit();
}

$resumeId = $_GET['id'] ?? null;
if (!$resumeId) {
    header("Location: myresumes.php");
    exit();
}

// Load existing data
$stmt = $db->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resumeId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc() ?: [];
$stmt->close();

if (!$data) {
    $fn->setError('Resume not found or access denied.');
    header("Location: myresumes.php");
    exit();
}

// Load related data
$experience = $education = $skills = $projects = $certificates = $achievements = [];
$stmt = $db->prepare("SELECT * FROM resume_experience WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$experience = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $db->prepare("SELECT * FROM resume_education WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $db->prepare("SELECT skill FROM resume_skills WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row['skill'];
}
$stmt->close();

$stmt = $db->prepare("SELECT project FROM resume_projects WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row['project'];
}
$stmt->close();

$stmt = $db->prepare("SELECT certificate FROM resume_certificates WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$certificates = [];
while ($row = $result->fetch_assoc()) {
    $certificates[] = $row['certificate'];
}
$stmt->close();

$stmt = $db->prepare("SELECT achievement FROM resume_achievements WHERE resume_id = ?");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$achievements = [];
while ($row = $result->fetch_assoc()) {
    $achievements[] = $row['achievement'];
}
$stmt->close();

$data['experience'] = $experience;
$data['education'] = $education;
$data['skills'] = $skills;
$data['projects'] = $projects;
$data['certificates'] = $certificates;
$data['achievements'] = $achievements;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_progress'])) {
        $db->begin_transaction();
        try {
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $mobile = $_POST['mobile'] ?? '';
            $dob = $_POST['dob'] ?? null;
            $gender = $_POST['gender'] ?? '';
            $religion = $_POST['religion'] ?? '';
            $nationality = $_POST['nationality'] ?? '';
            $marital_status = $_POST['marital_status'] ?? '';
            $hobbies = $_POST['hobbies'] ?? '';
            $languages = $_POST['languages'] ?? '';
            $address = $_POST['address'] ?? '';
            $linkedin = $_POST['linkedin'] ?? '';
            $github = $_POST['github'] ?? '';

            $stmt = $db->prepare("UPDATE resumes SET 
                full_name = ?, email = ?, mobile = ?, dob = ?, gender = ?,
                religion = ?, nationality = ?, marital_status = ?, hobbies = ?,
                languages = ?, address = ?, linkedin = ?, github = ?
                WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sssssssssssssii",
                $full_name, $email, $mobile, $dob, $gender,
                $religion, $nationality, $marital_status, $hobbies,
                $languages, $address, $linkedin, $github,
                $resumeId, $userId
            );
            $stmt->execute();
            $stmt->close();

            $db->commit();
            $fn->setAlert('Progress saved successfully!');
            header("Location: create_resume.php?id=$resumeId&user_id=$userId");
            exit();
        } catch (Exception $e) {
            $db->rollback();
            error_log("Error saving progress: " . $e->getMessage());
            $fn->setError('Error saving progress: ' . $e->getMessage());
            header("Location: create_resume.php?id=$resumeId&user_id=$userId");
            exit();
        }
    } elseif (isset($_POST['save_resume'])) {
        $db->begin_transaction();
        try {
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $mobile = $_POST['mobile'] ?? '';
            $dob = $_POST['dob'] ?? null;
            $gender = $_POST['gender'] ?? '';
            $religion = $_POST['religion'] ?? '';
            $nationality = $_POST['nationality'] ?? '';
            $marital_status = $_POST['marital_status'] ?? '';
            $hobbies = $_POST['hobbies'] ?? '';
            $languages = $_POST['languages'] ?? '';
            $address = $_POST['address'] ?? '';
            $linkedin = $_POST['linkedin'] ?? '';
            $github = $_POST['github'] ?? '';

            $stmt = $db->prepare("UPDATE resumes SET 
                full_name = ?, email = ?, mobile = ?, dob = ?, gender = ?,
                religion = ?, nationality = ?, marital_status = ?, hobbies = ?,
                languages = ?, address = ?, linkedin = ?, github = ?
                WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sssssssssssssii",
                $full_name, $email, $mobile, $dob, $gender,
                $religion, $nationality, $marital_status, $hobbies,
                $languages, $address, $linkedin, $github,
                $resumeId, $userId
            );
            $stmt->execute();
            $stmt->close();

            $tables = ['resume_experience', 'resume_education', 'resume_skills', 'resume_projects', 'resume_certificates', 'resume_achievements'];
            foreach ($tables as $table) {
                $stmt = $db->prepare("DELETE FROM $table WHERE resume_id = ?");
                $stmt->bind_param("i", $resumeId);
                $stmt->execute();
                $stmt->close();
            }

            if (!empty($_POST['job_title'])) {
                $stmt = $db->prepare("INSERT INTO resume_experience (resume_id, title, company, years) VALUES (?, ?, ?, ?)");
                foreach ($_POST['job_title'] as $i => $title) {
                    if (!empty($title) && !empty($_POST['company_name'][$i]) && !empty($_POST['years'][$i])) {
                        $company = $_POST['company_name'][$i];
                        $years = $_POST['years'][$i];
                        $stmt->bind_param("isss", $resumeId, $title, $company, $years);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            if (!empty($_POST['degree'])) {
                $stmt = $db->prepare("INSERT INTO resume_education (resume_id, degree, school, year) VALUES (?, ?, ?, ?)");
                foreach ($_POST['degree'] as $i => $degree) {
                    if (!empty($degree) && !empty($_POST['school_name'][$i]) && !empty($_POST['year_graduated'][$i])) {
                        $school = $_POST['school_name'][$i];
                        $year = $_POST['year_graduated'][$i];
                        $stmt->bind_param("isss", $resumeId, $degree, $school, $year);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            if (!empty($_POST['skills'])) {
                $stmt = $db->prepare("INSERT INTO resume_skills (resume_id, skill) VALUES (?, ?)");
                foreach ($_POST['skills'] as $skill) {
                    if (!empty($skill)) {
                        $stmt->bind_param("is", $resumeId, $skill);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            if (!empty($_POST['projects'])) {
                $stmt = $db->prepare("INSERT INTO resume_projects (resume_id, project) VALUES (?, ?)");
                foreach ($_POST['projects'] as $project) {
                    if (!empty($project)) {
                        $stmt->bind_param("is", $resumeId, $project);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            if (!empty($_POST['certificates'])) {
                $stmt = $db->prepare("INSERT INTO resume_certificates (resume_id, certificate) VALUES (?, ?)");
                foreach ($_POST['certificates'] as $certificate) {
                    if (!empty($certificate)) {
                        $stmt->bind_param("is", $resumeId, $certificate);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            if (!empty($_POST['achievements'])) {
                $stmt = $db->prepare("INSERT INTO resume_achievements (resume_id, achievement) VALUES (?, ?)");
                foreach ($_POST['achievements'] as $achievement) {
                    if (!empty($achievement)) {
                        $stmt->bind_param("is", $resumeId, $achievement);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }

            $db->commit();
            $fn->setAlert('Resume saved successfully!');
            header("Location: selecttemplate.php?id=$resumeId&user_id=$userId");
            exit();
        } catch (Exception $e) {
            $db->rollback();
            error_log("Error saving resume: " . $e->getMessage());
            $fn->setError('Error saving resume: ' . $e->getMessage());
            header("Location: myresumes.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Resume | Resume Builder</title>
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
            pointer-events: none;
            z-index: 1;
        }
        .shape-1 { width: 400px; height: 400px; top: 0; left: 0; }
        .shape-2 { width: 300px; height: 300px; bottom: 0; right: 0; }
        .shape-3 { width: 250px; height: 250px; top: 50%; left: 20%; }
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
        }
        .slide {
            flex: 0 0 100%;
            width: 100%;
            padding: 1rem;
            box-sizing: border-box;
        }
        .step-nav {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            background: #f9fafb;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }
        .step-nav button {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            font-weight: 500;
            transition: color 0.3s;
        }
        .step-nav button.active {
            color: #1d4ed8;
            font-weight: 600;
            background: #e0e7ff;
            border-radius: 0.375rem;
        }
        .progress-bar {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #f9fafb;
            border-radius: 0.375rem;
        }
        .progress {
            flex: 1;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #1d4ed8;
            transition: width 0.3s;
        }
        .progress-text {
            margin-left: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .save-progress {
            margin-left: 1rem;
            padding: 0.25rem 0.75rem;
            background: #4b6cb7;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .save-progress:hover {
            background: #3b5aa7;
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            position: relative;
            z-index: 20;
            pointer-events: auto !important;
        }
        #experience-container, #certificates-container, #achievements-container {
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body class="animated-background min-h-screen pb-4">
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

<!-- Form with Step Slider -->
<form method="POST" action="" class="container mx-auto mt-10 px-4">
    <div class="bg-white bg-opacity-90 w-full max-w-4xl mx-auto shadow-2xl rounded-xl p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Create Resume</h1>
        <hr class="my-2 border-gray-300">
        <div class="progress-bar">
            <div class="progress">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-text" id="progressText">0% complete</span>
            <!-- <button type="submit" name="save_progress" class="save-progress">Save Progress</button> -->
        </div>
        <div class="step-nav">
            <button type="button" class="step active" data-step="0">Personal Info</button>
            <button type="button" class="step" data-step="1">Experience</button>
            <button type="button" class="step" data-step="2">Projects</button>
            <button type="button" class="step" data-step="3">Education</button>
            <button type="button" class="step" data-step="4">Skills</button>
            <button type="button" class="step" data-step="5">Certificates</button>
            <button type="button" class="step" data-step="6">Achievements</button>
        </div>
        <div class="slider-container">
            <div class="slider">
                <!-- Slide 1: Personal Information -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="contact-book.png" class="h-8 w-8" alt="Personal Info Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Personal Information</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2">
                        <div>
                            <label class="block text-gray-700 font-medium">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($data['full_name'] ?? ''); ?>" placeholder="Full Name" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Email</label>
                            <input type="text" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" placeholder="Email@abc.com" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Mobile Number</label>
                            <input type="number" name="mobile" value="<?php echo htmlspecialchars($data['mobile'] ?? ''); ?>" placeholder="Mobile Number" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">LinkedIn</label>
                            <input type="text" name="linkedin" value="<?php echo htmlspecialchars($data['linkedin'] ?? ''); ?>" placeholder="LinkedIn Profile URL" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">GitHub</label>
                            <input type="text" name="github" value="<?php echo htmlspecialchars($data['github'] ?? ''); ?>" placeholder="GitHub Profile URL" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo htmlspecialchars($data['dob'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Gender</label>
                            <select name="gender" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" <?php echo !$data['gender'] ? 'selected' : ''; ?> disabled>Select your gender</option>
                                <option value="male" <?php echo $data['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $data['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo $data['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Religion</label>
                            <select name="religion" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" <?php echo !$data['religion'] ? 'selected' : ''; ?> disabled>Select your religion</option>
                                <option value="hindu" <?php echo $data['religion'] === 'hindu' ? 'selected' : ''; ?>>Hindu</option>
                                <option value="muslim" <?php echo $data['religion'] === 'muslim' ? 'selected' : ''; ?>>Muslim</option>
                                <option value="sikh" <?php echo $data['religion'] === 'sikh' ? 'selected' : ''; ?>>Sikh</option>
                                <option value="other" <?php echo $data['religion'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Nationality</label>
                            <select name="nationality" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" <?php echo !$data['nationality'] ? 'selected' : ''; ?> disabled>Select your nationality</option>
                                <option value="indian" <?php echo $data['nationality'] === 'indian' ? 'selected' : ''; ?>>Indian</option>
                                <option value="non-indian" <?php echo $data['nationality'] === 'non-indian' ? 'selected' : ''; ?>>Non-Indian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Marital Status</label>
                            <select name="marital_status" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" <?php echo !$data['marital_status'] ? 'selected' : ''; ?> disabled>Select your Marital Status</option>
                                <option value="married" <?php echo $data['marital_status'] === 'married' ? 'selected' : ''; ?>>Married</option>
                                <option value="single" <?php echo $data['marital_status'] === 'single' ? 'selected' : ''; ?>>Single</option>
                                <option value="divorced" <?php echo $data['marital_status'] === 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Hobbies</label>
                            <input type="text" name="hobbies" value="<?php echo htmlspecialchars($data['hobbies'] ?? ''); ?>" placeholder="Hobbies" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Languages Known</label>
                            <input type="text" name="languages" value="<?php echo htmlspecialchars($data['languages'] ?? ''); ?>" placeholder="Languages Known" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium">Address</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>" placeholder="Address" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 2: Experience -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="briefcase.png" class="h-8 w-8" alt="Experience Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Experience</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="experience-container" class="space-y-4">
                            <?php if (!empty($data['experience'])): ?>
                                <?php foreach ($data['experience'] as $exp): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="text" name="job_title[]" value="<?php echo htmlspecialchars($exp['title'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Job Title">
                                        <input type="text" name="company_name[]" value="<?php echo htmlspecialchars($exp['company'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Company Name">
                                        <input type="text" name="years[]" value="<?php echo htmlspecialchars($exp['years'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Years of Experience">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input type="text" name="job_title[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Job Title">
                                    <input type="text" name="company_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Company Name">
                                    <input type="text" name="years[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Years of Experience">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addExperienceBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 3: Projects -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="project.png" class="h-8 w-8" alt="Projects Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Projects With Description</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="projects-container" class="space-y-4">
                            <?php if (!empty($data['projects'])): ?>
                                <?php foreach ($data['projects'] as $project): ?>
                                    <div class="grid grid-cols-1 gap-4">
                                        <input type="text" name="projects[]" value="<?php echo htmlspecialchars($project ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project name...">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="text" name="projects[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project name...">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addProjectBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 4: Education -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="book.png" class="h-8 w-8" alt="Education Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Education</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="education-container" class="space-y-4">
                            <?php if (!empty($data['education'])): ?>
                                <?php foreach ($data['education'] as $edu): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="text" name="degree[]" value="<?php echo htmlspecialchars($edu['degree'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Degree">
                                        <input type="text" name="school_name[]" value="<?php echo htmlspecialchars($edu['school'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="School Name">
                                        <input type="text" name="year_graduated[]" value="<?php echo htmlspecialchars($edu['year'] ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Year Graduated">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input type="text" name="degree[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Degree">
                                    <input type="text" name="school_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="School Name">
                                    <input type="text" name="year_graduated[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Year Graduated">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addEducationBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 5: Skills -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="dimensions.png" class="h-8 w-8" alt="Skills Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Skills</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="skills-container" class="space-y-4">
                            <?php if (!empty($data['skills'])): ?>
                                <?php foreach ($data['skills'] as $skill): ?>
                                    <div class="grid grid-cols-1 gap-4">
                                        <input type="text" name="skills[]" value="<?php echo htmlspecialchars($skill ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your skill...">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="text" name="skills[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your skill...">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addSkillBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 6: Certificates -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="https://static.vecteezy.com/system/resources/previews/005/911/684/large_2x/business-agreement-icon-certificate-symbol-for-your-web-site-logo-app-ui-design-free-vector.jpg" class="h-8 w-8" alt="Certificates Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Certificates</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="certificates-container" class="space-y-4">
                            <?php if (!empty($data['certificates'])): ?>
                                <?php foreach ($data['certificates'] as $certificate): ?>
                                    <div class="grid grid-cols-1 gap-4">
                                        <input type="text" name="certificates[]" value="<?php echo htmlspecialchars($certificate ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your certificate...">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="text" name="certificates[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your certificate...">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addCertificateBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                    <div class="flex justify-end mt-4">
                        <button type="button" class="next-btn bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Next</button>
                    </div>
                </div>

                <!-- Slide 7: Achievements -->
                <div class="slide">
                    <div class="flex items-center space-x-3 p-2">
                        <img src="https://static.vecteezy.com/system/resources/previews/033/901/435/large_2x/winner-success-icon-symbol-image-illustration-of-reward-champion-win-championship-bedge-image-design-vector.jpg" class="h-8 w-8" alt="Achievements Icon">
                        <h2 class="text-2xl font-semibold text-gray-600">Achievements</h2>
                    </div>
                    <div class="p-2 space-y-6">
                        <div id="achievements-container" class="space-y-4">
                            <?php if (!empty($data['achievements'])): ?>
                                <?php foreach ($data['achievements'] as $achievement): ?>
                                    <div class="grid grid-cols-1 gap-4">
                                        <input type="text" name="achievements[]" value="<?php echo htmlspecialchars($achievement ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your achievement...">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="grid grid-cols-1 gap-4">
                                    <input type="text" name="achievements[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your achievement...">
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="addAchievementBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
                    </div>
                    <hr class="my-4 border-gray-300">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
            <button type="submit" name="save_resume" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2zm-9-5h4v4h-4zm-1-6h6V6h-6z"></path>
                </svg>
                Save Resume
            </button>
        </div>
    </div>
</form>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const steps = document.querySelectorAll('.step');
        const nextButtons = document.querySelectorAll('.next-btn');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        let currentStep = 0;
        const totalSteps = 7; // Personal Info, Experience, Projects, Education, Skills, Certificates, Achievements

        function updateSlider() {
            const progress = ((currentStep + 1) / totalSteps) * 100;
            progressFill.style.width = `${progress}%`;
            progressText.textContent = `${Math.round(progress)}% complete`;
            slider.style.transform = `translateX(-${currentStep * 100}%)`;
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === currentStep);
            });
        }

        // Handle step navigation via step buttons
        steps.forEach(step => {
            step.addEventListener('click', function() {
                const stepIndex = parseInt(this.getAttribute('data-step'));
                if (stepIndex >= 0 && stepIndex < totalSteps) {
                    currentStep = stepIndex;
                    updateSlider();
                }
            });
        });

        // Handle next button clicks
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (currentStep < totalSteps - 1) {
                    currentStep++;
                    updateSlider();
                }
            });
        });

        updateSlider();

        // Experience
        const addExperienceBtn = document.getElementById('addExperienceBtn');
        if (addExperienceBtn) {
            addExperienceBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('experience-container');
                if (!container) {
                    console.error('Experience container not found!');
                    return;
                }
                const newExperience = document.createElement('div');
                newExperience.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-4');
                newExperience.innerHTML = `
                    <input type="text" name="job_title[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Job Title">
                    <input type="text" name="company_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Company Name">
                    <input type="text" name="years[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Years of Experience">
                `;
                container.appendChild(newExperience);
            });
        }

        // Projects
        const addProjectBtn = document.getElementById('addProjectBtn');
        if (addProjectBtn) {
            addProjectBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('projects-container');
                if (!container) {
                    console.error('Projects container not found!');
                    return;
                }
                const newProject = document.createElement('div');
                newProject.classList.add('grid', 'grid-cols-1', 'gap-4');
                newProject.innerHTML = `
                    <input type="text" name="projects[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project name...">
                `;
                container.appendChild(newProject);
            });
        }

        // Education
        const addEducationBtn = document.getElementById('addEducationBtn');
        if (addEducationBtn) {
            addEducationBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('education-container');
                if (!container) {
                    console.error('Education container not found!');
                    return;
                }
                const newEducation = document.createElement('div');
                newEducation.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-4');
                newEducation.innerHTML = `
                    <input type="text" name="degree[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Degree">
                    <input type="text" name="school_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="School Name">
                    <input type="text" name="year_graduated[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Year Graduated">
                `;
                container.appendChild(newEducation);
            });
        }

        // Skills
        const addSkillBtn = document.getElementById('addSkillBtn');
        if (addSkillBtn) {
            addSkillBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('skills-container');
                if (!container) {
                    console.error('Skills container not found!');
                    return;
                }
                const newSkill = document.createElement('div');
                newSkill.classList.add('grid', 'grid-cols-1', 'gap-4');
                newSkill.innerHTML = `
                    <input type="text" name="skills[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your skill...">
                `;
                container.appendChild(newSkill);
            });
        }

        // Certificates
        const addCertificateBtn = document.getElementById('addCertificateBtn');
        if (addCertificateBtn) {
            addCertificateBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('certificates-container');
                if (!container) {
                    console.error('Certificates container not found!');
                    return;
                }
                const newCertificate = document.createElement('div');
                newCertificate.classList.add('grid', 'grid-cols-1', 'gap-4');
                newCertificate.innerHTML = `
                    <input type="text" name="certificates[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your certificate...">
                `;
                container.appendChild(newCertificate);
            });
        }

        // Achievements
        const addAchievementBtn = document.getElementById('addAchievementBtn');
        if (addAchievementBtn) {
            addAchievementBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const container = document.getElementById('achievements-container');
                if (!container) {
                    console.error('Achievements container not found!');
                    return;
                }
                const newAchievement = document.createElement('div');
                newAchievement.classList.add('grid', 'grid-cols-1', 'gap-4');
                newAchievement.innerHTML = `
                    <input type="text" name="achievements[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your achievement...">
                `;
                container.appendChild(newAchievement);
            });
        }
    });
</script>
</body>
</html>