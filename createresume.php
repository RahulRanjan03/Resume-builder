<?php
// src/createresume.php
require 'function.class.php';
$fn->AuthPage();

// Get the resume ID from the URL
$resumeId = $_GET['id'] ?? null;
if (!$resumeId || !isset($_SESSION['resumes'][$resumeId])) {
    header("Location: myresumes.php");
    exit();
}

// Load existing data if available
$data = $_SESSION['resumes'][$resumeId] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_resume'])) {
    // Save form data to the specific resume in the session
    $_SESSION['resumes'][$resumeId] = [
        'full_name' => $_POST['full_name'] ?? $data['full_name'],
        'email' => $_POST['email'] ?? $data['email'],
        'mobile' => $_POST['mobile'] ?? $data['mobile'],
        'dob' => $_POST['dob'] ?? $data['dob'],
        'gender' => $_POST['gender'] ?? $data['gender'],
        'religion' => $_POST['religion'] ?? $data['religion'],
        'nationality' => $_POST['nationality'] ?? $data['nationality'],
        'marital_status' => $_POST['marital_status'] ?? $data['marital_status'],
        'hobbies' => $_POST['hobbies'] ?? $data['hobbies'],
        'languages' => $_POST['languages'] ?? $data['languages'],
        'address' => $_POST['address'] ?? $data['address'],
        'linkedin' => $_POST['linkedin'] ?? $data['linkedin'],
        'github' => $_POST['github'] ?? $data['github'],
        'experience' => array_map(function($title, $company, $years) {
            return ['title' => $title, 'company' => $company, 'years' => $years];
        }, $_POST['job_title'] ?? [], $_POST['company_name'] ?? [], $_POST['years'] ?? []),
        'education' => array_map(function($degree, $school, $year) {
            return ['degree' => $degree, 'school' => $school, 'year' => $year];
        }, $_POST['degree'] ?? [], $_POST['school_name'] ?? [], $_POST['year_graduated'] ?? []),
        'skills' => $_POST['skills'] ?? $data['skills'] ?? [],
        'projects' => $_POST['projects'] ?? $data['projects'] ?? [],
        'template' => $data['template'] ?? null
    ];
    header("Location: selecttemplate.php?id=$resumeId");
    exit();
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
</head>
<body class="bg-[url('https://img.freepik.com/free-photo/blue-toned-pack-paper-sheets-with-copy-space_23-2148320442.jpg?t=st=1743925575~exp=1743929175~hmac=454505f420a8086c800cc2543a06eb6272cc850da49eea362eebd824e57ba727&w=1380')] bg-cover bg-center font-['Poppins'] min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white bg-opacity-95 h-16 px-6 py-4 flex justify-between items-center shadow-xl sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <img src="logo.png" class="h-8 w-8" alt="Logo">
            <h1 class="text-2xl font-bold text-gray-800">Resume Builder</h1>
        </div>
        <div class="flex space-x-4">
            <button class="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-800 transition duration-300">Profile</button>
            <a href="logout.actions.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">Logout</a>
        </div>
    </nav>

    <!-- Form -->
    <form method="POST" action="" class="container mx-auto mt-10 px-4">
        <div class="bg-white bg-opacity-90 w-full max-w-4xl mx-auto shadow-2xl rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Create Resume</h1>
            <hr class="my-2 border-gray-300">

            <!-- Personal Information -->
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

            <!-- Experience -->
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

            <!-- Projects -->
            <div class="flex items-center space-x-3 p-2">
                <img src="project.png" class="h-8 w-8" alt="Projects Icon">
                <h2 class="text-2xl font-semibold text-gray-600">Projects</h2>
            </div>
            <div class="p-2 space-y-6">
                <div id="projects-container" class="space-y-4">
                    <?php if (!empty($data['projects'])): ?>
                        <?php foreach ($data['projects'] as $project): ?>
                            <div class="grid grid-cols-1 gap-4">
                                <input type="text" name="projects[]" value="<?php echo htmlspecialchars($project ?? ''); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project...">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" name="projects[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project...">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" id="addProjectBtn" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300">Add More</button>
            </div>
            <hr class="my-4 border-gray-300">

            <!-- Education -->
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

            <!-- Skills -->
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
        document.getElementById('addExperienceBtn').addEventListener('click', function() {
            const container = document.getElementById('experience-container');
            const newExperience = document.createElement('div');
            newExperience.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-4');
            newExperience.innerHTML = `
                <input type="text" name="job_title[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Job Title">
                <input type="text" name="company_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Company Name">
                <input type="text" name="years[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Years of Experience">
            `;
            container.appendChild(newExperience);
        });

        document.getElementById('addProjectBtn').addEventListener('click', function() {
            const container = document.getElementById('projects-container');
            const newProject = document.createElement('div');
            newProject.classList.add('grid', 'grid-cols-1', 'gap-4');
            newProject.innerHTML = `
                <input type="text" name="projects[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project...">
            `;
            container.appendChild(newProject);
        });

        document.getElementById('addEducationBtn').addEventListener('click', function() {
            const container = document.getElementById('education-container');
            const newEducation = document.createElement('div');
            newEducation.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-4');
            newEducation.innerHTML = `
                <input type="text" name="degree[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Degree">
                <input type="text" name="school_name[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="School Name">
                <input type="text" name="year_graduated[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Year Graduated">
            `;
            container.appendChild(newEducation);
        });

        document.getElementById('addSkillBtn').addEventListener('click', function() {
            const container = document.getElementById('skills-container');
            const newSkill = document.createElement('div');
            newSkill.classList.add('grid', 'grid-cols-1', 'gap-4');
            newSkill.innerHTML = `
                <input type="text" name="skills[]" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your skill...">
            `;
            container.appendChild(newSkill);
        });
    </script>
</body>
</html>