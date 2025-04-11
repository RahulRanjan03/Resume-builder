<?php
require 'function.class.php';
require '../src/database.class.php';

$fn->AuthPage();
$userId = $_SESSION['user_id'] ?? 0;

$resumeId = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? null;
if (!$resumeId) {
    header("Location: myresumes.php");
    exit();
}

// Fetch resume data
$stmt = $db->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resumeId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc() ?: [];
$stmt->close();

if (!$data) {
    header("Location: myresumes.php");
    exit();
}

// Fetch related data
$experience = $education = $skills = $projects = [];

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

// Combine data
$data['experience'] = $experience;
$data['education'] = $education;
$data['skills'] = $skills;
$data['projects'] = $projects;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Template 3 | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap');
        #resumeContent {
            width: 210mm;
            height: 297mm;
            box-sizing: border-box;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: none;
            }
            #resumeContent {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body class="bg-[url('https://img.freepik.com/free-photo/blue-toned-pack-paper-sheets-with-copy-space_23-2148320442.jpg?t=st=1743925575~exp=1743929175~hmac=454505f420a8086c800cc2543a06eb6272cc850da49eea362eebd824e57ba727&w=1380')]  bg-cover bg-center flex justify-center py-10">
    <div class="w-full max-w-4xl">
        <?php if ($mode === 'view'): ?>
            <div class="bg-white p-4 mb-4 shadow-lg w-[210mm] rounded-lg flex justify-between items-center">
                <div class="flex space-x-4">
                    <label for="fontSelect" class="mr-2">Change Font:</label>
                    <select id="fontSelect" class="border p-2 rounded">
                        <option value="Roboto">Roboto</option>
                        <option value="Open Sans">Open Sans</option>
                        <option value="Lora">Lora</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button id="printBtn" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700">Print</button>
                    <button id="downloadBtn" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Download</button>
                    <button id="shareBtn" class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600">Share</button>
                    <button id="atsCheckBtn" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Check ATS</button>
                    <a href="myresumes.php" id="backBtn" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">Back</a>
                </div>
            </div>
        <?php endif; ?>
        <div id="resumeContent" class="bg-white shadow-lg p-10 border border-gray-300">
            <div class="border-l-4 border-green-500 pl-4">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($data['full_name'] ?? ''); ?></h1>
                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($data['address'] ?? ''); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($data['email'] ?? ''); ?> | <?php echo htmlspecialchars($data['mobile'] ?? ''); ?></p>
                <p class="text-gray-600">LinkedIn: <?php echo htmlspecialchars($data['linkedin'] ?? ''); ?> | GitHub: <?php echo htmlspecialchars($data['github'] ?? ''); ?></p>
            </div>
            <div class="mt-6">
                <!-- <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Objective</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4">
                        <?php echo htmlspecialchars($data['objective'] ?? 'Creative and detail-oriented professional with experience in ' . ($data['experience'][0]['title'] ?? 'floral design') . '. Seeking to leverage expertise and skills to excel in the field.'); ?>
                    </p>
                </div> -->
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Skills & Abilities</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4">
                        <?php echo implode(', ', array_map('htmlspecialchars', $data['skills'] ?? [])); ?>
                    </p>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Experience</h2>
                    <?php foreach ($data['experience'] ?? [] as $exp): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold"><?php echo htmlspecialchars($exp['company'] ?? ''); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($exp['years'] ?? ''); ?></p>
                            <p class="text-gray-700"><?php echo htmlspecialchars($exp['title'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($data['experience'])): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold">ANISE FLORISTS</p>
                            <p class="text-gray-600">JAN 20XX - PRESENT</p>
                            <p class="text-gray-700">Led a team of designers in creating custom floral arrangements for events.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Projects</h2>
                    <?php foreach ($data['projects'] ?? [] as $project): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-gray-700"><?php echo htmlspecialchars($project); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($data['projects'])): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold">Portfolio Website</p>
                            <p class="text-gray-700">A personal portfolio built with HTML, CSS, and JavaScript.</p>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold">Task Manager App</p>
                            <p class="text-gray-700">A web app developed using React and Node.js for task tracking.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Education</h2>
                    <?php foreach ($data['education'] ?? [] as $edu): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold"><?php echo htmlspecialchars($edu['school'] ?? ''); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($edu['degree'] ?? ''); ?> | <?php echo htmlspecialchars($edu['year'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($data['education'])): ?>
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="font-bold">CLOVER COLLEGE OF THE ARTS</p>
                            <p class="text-gray-600">JACKSONVILLE, FL | FLORAL DESIGN</p>
                            <p class="text-gray-700">Graduated with a 3.9 GPA, Dean’s List honoree.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Languages Known</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4"><?php echo htmlspecialchars($data['languages'] ?? ''); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Hobbies</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4"><?php echo htmlspecialchars($data['hobbies'] ?? ''); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Address</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4"><?php echo htmlspecialchars($data['address'] ?? ''); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-green-600 font-bold uppercase">Marital Status</h2>
                    <p class="text-gray-700 border-l-4 border-green-500 pl-4"><?php echo htmlspecialchars($data['marital_status'] ?? ''); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php if ($mode === 'view'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const resumeContent = document.getElementById('resumeContent');
            const fontSelect = document.getElementById('fontSelect');
            const printBtn = document.getElementById('printBtn');
            const downloadBtn = document.getElementById('downloadBtn');
            const shareBtn = document.getElementById('shareBtn');
            const atsCheckBtn = document.getElementById('atsCheckBtn');

            fontSelect.addEventListener('change', function() {
                resumeContent.style.fontFamily = this.value;
            });

            printBtn.addEventListener('click', function() {
                window.print();
            });

            downloadBtn.addEventListener('click', function() {
                const element = resumeContent.cloneNode(true);
                const fontFamily = fontSelect.value || 'Roboto';
                element.style.fontFamily = fontFamily;
                element.style.width = '190mm';
                element.style.height = '297mm';
                element.style.margin = '0 auto';
                element.style.boxShadow = '0 0 10mm rgba(0, 0, 0, 0.3)';
                element.style.backgroundColor = 'white';
                element.style.padding = '15mm';
                element.style.boxSizing = 'border-box';

                const opt = {
                    margin: [15, 10],
                    filename: 'resume.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save();
            });

            shareBtn.addEventListener('click', function() {
                const shareUrl = window.location.href;
                if (navigator.share) {
                    navigator.share({
                        title: 'My Resume',
                        text: 'Check out my resume!',
                        url: shareUrl
                    }).then(() => {
                        console.log('Successfully shared');
                    }).catch((error) => {
                        console.error('Error sharing:', error);
                        alert('Sharing failed. Please try copying the link manually.');
                    });
                } else {
                    Swal.fire({
                        title: 'Share Options',
                        html: `
                            <button id="copyBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-2 w-full">Copy to Clipboard</button>
                            <p class="text-center text-gray-600">Note: Web sharing is not supported on this device. Use copy or share manually.</p>
                        `,
                        showConfirmButton: false,
                        didOpen: () => {
                            document.getElementById('copyBtn').addEventListener('click', () => {
                                navigator.clipboard.writeText(shareUrl).then(() => {
                                    Swal.fire({
                                        title: 'Copied!',
                                        text: 'Link copied to clipboard: ' + shareUrl,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                }).catch(err => {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Failed to copy to clipboard.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            });
                        }
                    });
                }
            });

            atsCheckBtn.addEventListener('click', function() {
                const content = resumeContent.innerText.toLowerCase();
                let score = 0;
                let feedback = [];

                if (content.includes('experience')) score += 20;
                else feedback.push('Add an "Experience" section.');
                if (content.includes('education')) score += 20;
                else feedback.push('Add an "Education" section.');
                if (content.includes('skills')) score += 20;
                else feedback.push('Add a "Skills" section.');

                const keywords = ['software', 'developer', 'project', 'team', 'management'];
                let keywordCount = 0;
                keywords.forEach(keyword => {
                    const regex = new RegExp(keyword, 'g');
                    const matches = (content.match(regex) || []).length;
                    keywordCount += matches;
                });
                if (keywordCount > 5) score += 20;
                else feedback.push('Include more job-relevant keywords (e.g., software, project).');

                const wordCount = content.split(/\s+/).length;
                if (wordCount > 100 && wordCount < 500) score += 20;
                else if (wordCount <= 100) feedback.push('Resume is too short; add more details.');
                else feedback.push('Resume may be too long; keep it concise.');

                Swal.fire({
                    title: `ATS Score: ${score}%`,
                    html: feedback.length > 0 ? 
                        `<p><strong>Feedback:</strong></p><ul class="list-disc pl-5">${feedback.map(f => `<li>${f}</li>`).join('')}</ul>` : 
                        '<p>Your resume looks ATS-friendly!</p>',
                    icon: score >= 80 ? 'success' : 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>