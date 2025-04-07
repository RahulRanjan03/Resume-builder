<?php
// src/myresumes.php
// session_start();
require 'function.class.php';
$fn->AuthPage();

// Initialize resumes array if it doesn't exist
if (!isset($_SESSION['resumes'])) {
    $_SESSION['resumes'] = [];
}

// Handle "Add New Resume" submission from the popup form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_resume'])) {
    $resumeName = $_POST['resume_name'] ?? 'New Resume ' . (count($_SESSION['resumes']) + 1);
    $newResume = [
        'full_name' => $resumeName,
        'email' => '',
        'mobile' => '',
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
        'projects' => [],
        'template' => null
    ];
    $resumeId = count($_SESSION['resumes']) + 1;
    $_SESSION['resumes'][$resumeId] = $newResume;
    
    header("Location: myresumes.php");
    exit();
}

// Handle "Delete Resume" submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_resume'])) {
    $resumeId = $_POST['resume_id'];
    unset($_SESSION['resumes'][$resumeId]);
    
    header("Location: myresumes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resumes | Resume Builder</title>
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
            <button class="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-800 transition duration-300">Profile</button>
            <a href="logout.actions.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">
        <div class="bg-white bg-opacity-90 w-full max-w-4xl mx-auto shadow-2xl rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Your Resumes</h1>
            <hr class="my-2 border-gray-300">

            <!-- Add New Resume Button -->
            <div class="flex justify-end mb-6">
                <button id="addResumeBtn" class="bg-blue-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-blue-700 hover:scale-105 transition duration-300">
                    + Add New Resume
                </button>
            </div>

            <!-- Resume List -->
            <div id="resumeList" class="space-y-4">
                <?php if (!empty($_SESSION['resumes'])): ?>
                    <?php foreach ($_SESSION['resumes'] as $id => $resume): ?>
                        <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg shadow-md hover:bg-gray-200 hover:translate-x-1 transition duration-300" data-id="<?php echo $id; ?>">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($resume['full_name']); ?></h3>
                                <p class="text-sm text-gray-600">
                                    <?php echo $resume['template'] ? "Template " . $resume['template'] : "Not yet completed"; ?>
                                </p>
                            </div>
                            <div class="flex space-x-3">
                                <?php if ($resume['template']): ?>
                                    <a href="viewresume.php?id=<?php echo $id; ?>" class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition duration-300">View</a>
                                <?php endif; ?>
                                <a href="createresume.php?id=<?php echo $id; ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 transition duration-300">Edit</a>
                                <button class="delete-resume bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300" data-id="<?php echo $id; ?>">Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600 text-center py-6 text-lg">No resumes created yet. Start by adding one!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        $fn->error();
        $fn->alert();
        ?>

        // Add New Resume with Popup
        document.getElementById('addResumeBtn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Add New Resume',
                html: '<input id="resumeName" class="swal2-input" placeholder="Enter resume name">',
                showCancelButton: true,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const resumeName = document.getElementById('resumeName').value;
                    if (!resumeName) {
                        Swal.showValidationMessage('Please enter a resume name');
                        return false;
                    }
                    return resumeName;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const resumeName = result.value;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    form.innerHTML = `
                        <input type="hidden" name="add_new_resume" value="1">
                        <input type="hidden" name="resume_name" value="${resumeName}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Delete Resume
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-resume')) {
                e.preventDefault();
                const resumeId = e.target.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        form.innerHTML = `
                            <input type="hidden" name="delete_resume" value="1">
                            <input type="hidden" name="resume_id" value="${resumeId}">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    </script>
</body>
</html>