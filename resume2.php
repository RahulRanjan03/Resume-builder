<?php
require 'function.class.php';
$fn->AuthPage();

$resumeId = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? null;
if (!$resumeId || !isset($_SESSION['resumes'][$resumeId])) {
    header("Location: myresumes.php");
    exit();
}
$data = $_SESSION['resumes'][$resumeId];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Template 2 | Resume Builder</title>
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
<body class="bg-[url('https://img.freepik.com/free-photo/blue-toned-pack-paper-sheets-with-copy-space_23-2148320442.jpg?t=st=1743925575~exp=1743929175~hmac=454505f420a8086c800cc2543a06eb6272cc850da49eea362eebd824e57ba727&w=1380')]  bg-cover bg-center flex justify-center items-center py-10">
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
        <div id="resumeContent" class="border border-gray-300 bg-white shadow-lg p-8 rounded-lg">
            <div class="text-center border-b pb-4 mb-4">
                <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($data['full_name'] ?? ''); ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($data['experience'][0]['title'] ?? ''); ?></p>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Personal Information</h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email'] ?? ''); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($data['mobile'] ?? ''); ?></p>
                <p><strong>LinkedIn:</strong> <?php echo htmlspecialchars($data['linkedin'] ?? ''); ?></p>
                <p><strong>GitHub:</strong> <?php echo htmlspecialchars($data['github'] ?? ''); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($data['address'] ?? ''); ?></p>
                <p><strong>Nationality:</strong> <?php echo htmlspecialchars($data['nationality'] ?? ''); ?></p>
                <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($data['marital_status'] ?? ''); ?></p>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Skills</h2>
                <ul class="list-disc pl-5">
                    <?php foreach ($data['skills'] ?? [] as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($data['skills'])): ?>
                        <li>Skill 1</li>
                        <li>Skill 2</li>
                        <li>Skill 3</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Experience</h2>
                <?php foreach ($data['experience'] ?? [] as $exp): ?>
                    <p><strong><?php echo htmlspecialchars($exp['title'] ?? ''); ?> - <?php echo htmlspecialchars($exp['company'] ?? ''); ?></strong></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($exp['years'] ?? ''); ?></p>
                <?php endforeach; ?>
                <?php if (empty($data['experience'])): ?>
                    <p><strong>Job Title - Company Name</strong></p>
                    <p class="text-gray-600">Years of Experience | Location</p>
                    <p>Description of responsibilities and achievements.</p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Projects</h2>
                <?php foreach ($data['projects'] ?? [] as $project): ?>
                    <p><?php echo htmlspecialchars($project); ?></p>
                <?php endforeach; ?>
                <?php if (empty($data['projects'])): ?>
                    <p><strong>Portfolio Website</strong> - A personal portfolio built with HTML, CSS, and JavaScript.</p>
                    <p><strong>Task Manager App</strong> - A web app developed using React and Node.js for task tracking.</p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Education</h2>
                <?php foreach ($data['education'] ?? [] as $edu): ?>
                    <p><strong><?php echo htmlspecialchars($edu['degree'] ?? ''); ?> - <?php echo htmlspecialchars($edu['school'] ?? ''); ?></strong></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($edu['year'] ?? ''); ?></p>
                <?php endforeach; ?>
                <?php if (empty($data['education'])): ?>
                    <p><strong>Degree - School Name</strong></p>
                    <p class="text-gray-600">Year Graduated</p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Languages Known</h2>
                <p><?php echo htmlspecialchars($data['languages'] ?? ''); ?></p>
            </div>
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Hobbies</h2>
                <p><?php echo htmlspecialchars($data['hobbies'] ?? ''); ?></p>
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