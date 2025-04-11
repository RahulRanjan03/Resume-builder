<!-- <?php
// session_start();
require 'function.class.php';
require '../src/database.class.php';

$fn->AuthPage();
$userId = $_SESSION['user_id'] ?? 0;

if (!$userId) {
    $fn->setError('Please log in to upload a CV.');
    $fn->redirect('login.php');
    exit();
}

// Initialize variables for form data
$data = [
    'full_name' => '',
    'email' => '',
    'mobile' => '',
    'linkedin' => '',
    'github' => '',
    'dob' => '',
    'gender' => '',
    'religion' => '',
    'nationality' => '',
    'marital_status' => '',
    'hobbies' => '',
    'languages' => '',
    'address' => '',
    'experience' => [],
    'education' => [],
    'skills' => [],
    'projects' => []
];

// Handle CV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) { // Line 32
    $file = $_FILES['cv_file'];
    if ($file['error'] === UPLOAD_ERR_OK) { // Line 34
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . uniqid() . '-' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $filePath);

        // Attempt to extract text (only works reliably for .txt files)
        $fileType = mime_content_type($filePath);
        if ($fileType === 'text/plain') { // Line 43 (This is likely your Line 53)
            $text = file_get_contents($filePath);
            $lines = explode("\n", $text);
            foreach ($lines as $line) { // Line 46
                $line = trim($line);
                if (empty($line)) continue;

                if (empty($data['full_name']) && !str_contains($line, '@') && !str_contains($line, 'http')) { // Line 50 (This is likely your Line 67)
                    $data['full_name'] = $line;
                } elseif (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                    $data['email'] = $line;
                } elseif (preg_match('/\b\d{10}\b/', $line, $matches)) {
                    $data['mobile'] = $matches[0];
                } elseif (str_contains($line, 'linkedin.com')) {
                    $data['linkedin'] = $line;
                } elseif (str_contains($line, 'github.com')) {
                    $data['github'] = $line;
                } elseif (str_contains(strtolower($line), 'experience') || str_contains(strtolower($line), 'work')) {
                    $data['experience'][] = ['title' => '', 'company' => '', 'years' => $line];
                } elseif (str_contains(strtolower($line), 'education') || str_contains(strtolower($line), 'university')) {
                    $data['education'][] = ['degree' => '', 'school' => $line, 'year' => ''];
                } elseif (str_contains(strtolower($line), 'skills')) {
                    $data['skills'] = explode(',', $line);
                } elseif (str_contains(strtolower($line), 'project')) {
                    $data['projects'][] = $line;
                }
            } // Closing foreach
        } else { // Line 67
            $fn->setAlert('Uploaded file is not a text file. Please fill in the form manually or copy-paste your CV content.');
        } // Closing if ($fileType)
        unlink($filePath); // Clean up uploaded file
    } else { // Line 71
        $fn->setError('Error uploading file. Please try again.');
    } // Closing if ($file['error'])
} // Closing if ($_SERVER)

// Handle form save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cv'])) { // Line 76
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

        // Insert new resume
        $stmt = $db->prepare("INSERT INTO resumes (user_id, full_name, email, mobile, dob, gender, religion, nationality, marital_status, hobbies, languages, address, linkedin, github) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssssss", $userId, $full_name, $email, $mobile, $dob, $gender, $religion, $nationality, $marital_status, $hobbies, $languages, $address, $linkedin, $github);
        $stmt->execute();
        $resumeId = $db->insert_id;
        $stmt->close();

        // Save experience
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

        // Save education
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

        // Save skills
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

        // Save projects
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

        $db->commit();
        $fn->setAlert('CV saved successfully!');
        header("Location: selecttemplate.php?id=$resumeId");
        exit();
    } catch (Exception $e) {
        $db->rollback();
        $fn->setError('Error saving CV: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CV | Resume Builder</title>
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
            <a href="myresumes.php" class="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-800 transition duration-300">Back to Resumes</a>
            <a href="logout.actions.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">
        <div class="bg-white bg-opacity-90 w-full max-w-4xl mx-auto shadow-2xl rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Upload and Modify Your CV</h1>
            <hr class="my-2 border-gray-300">

            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data" class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Upload Your CV (PDF, Word, or Text)</label>
                <input type="file" name="cv_file" accept=".txt,.pdf,.doc,.docx" class="w-full border-2 border-gray-300 rounded-md p-2 mb-4">
                <p class="text-gray-600 text-sm mb-4">Note: Only text files (.txt) will be automatically parsed. For PDFs or Word docs, please copy-paste content into the form below.</p>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition duration-300">Upload CV</button>
            </form>

            <!-- Edit Form -->
            <form method="POST" action="" class="space-y-6">
                <!-- Personal Information -->
                <div class="flex items-center space-x-3 p-2">
                    <img src="contact-book.png" class="h-8 w-8" alt="Personal Info Icon">
                    <h2 class="text-2xl font-semibold text-gray-600">Personal Information</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2">
                    <div>
                        <label class="block text-gray-700 font-medium">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($data['full_name']); ?>" placeholder="Full Name" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Email</label>
                        <input type="text" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" placeholder="Email@abc.com" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Mobile Number</label>
                        <input type="number" name="mobile" value="<?php echo htmlspecialchars($data['mobile']); ?>" placeholder="Mobile Number" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">LinkedIn</label>
                        <input type="text" name="linkedin" value="<?php echo htmlspecialchars($data['linkedin']); ?>" placeholder="LinkedIn Profile URL" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">GitHub</label>
                        <input type="text" name="github" value="<?php echo htmlspecialchars($data['github']); ?>" placeholder="GitHub Profile URL" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($data['dob']); ?>" class="w-full border-2 border-gray-300 rounded-md p-2 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <input type="text" name="hobbies" value="<?php echo htmlspecialchars($data['hobbies']); ?>" placeholder="Hobbies" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Languages Known</label>
                        <input type="text" name="languages" value="<?php echo htmlspecialchars($data['languages']); ?>" placeholder="Languages Known" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium">Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($data['address']); ?>" placeholder="Address" class="w-full border-2 border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    <input type="text" name="job_title[]" value="<?php echo htmlspecialchars($exp['title']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Job Title">
                                    <input type="text" name="company_name[]" value="<?php echo htmlspecialchars($exp['company']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Company Name">
                                    <input type="text" name="years[]" value="<?php echo htmlspecialchars($exp['years']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Years of Experience">
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
                                    <input type="text" name="projects[]" value="<?php echo htmlspecialchars($project); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your project...">
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
                                    <input type="text" name="degree[]" value="<?php echo htmlspecialchars($edu['degree']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Degree">
                                    <input type="text" name="school_name[]" value="<?php echo htmlspecialchars($edu['school']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="School Name">
                                    <input type="text" name="year_graduated[]" value="<?php echo htmlspecialchars($edu['year']); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Year Graduated">
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
                                    <input type="text" name="skills[]" value="<?php echo htmlspecialchars($skill); ?>" class="border-2 border-gray-300 p-2 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your skill...">
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
                    <button type="submit" name="save_cv" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 hover:scale-105 transition duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2zm-9-5h4v4h-4zm-1-6h6V6h-6z"></path>
                        </svg>
                        Save CV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        $fn->error();
        $fn->alert();
        ?>

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
</html> -->