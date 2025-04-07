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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Template 1 | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            body * {
                visibility: hidden;
            }
            #resumeContent, #resumeContent * {
                visibility: visible;
            }
            #resumeContent {
                position: absolute;
                top: 0;
                left: 0;
                width: 210mm;
                height: 297mm;
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
        <div id="resumeContent" class="bg-white shadow-lg p-10">
            <header class="text-center border-b pb-4">
                <h1 class="text-4xl font-bold"><?php echo htmlspecialchars($data['full_name'] ?? ''); ?></h1>
                <p class="text-lg text-gray-600"><?php echo htmlspecialchars($data['experience'][0]['title'] ?? ''); ?></p>
                <div class="mt-2 text-gray-600">
                    <p>LinkedIn: <?php echo htmlspecialchars($data['linkedin'] ?? ''); ?></p>
                    <p>GitHub: <?php echo htmlspecialchars($data['github'] ?? ''); ?></p>
                </div>
            </header>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Personal Information</h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-2 mt-2">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email'] ?? ''); ?></p>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($data['mobile'] ?? ''); ?></p>
                    <p><strong>Nationality:</strong> <?php echo htmlspecialchars($data['nationality'] ?? ''); ?></p>
                    <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($data['marital_status'] ?? ''); ?></p>
                    <p><strong>Languages Known:</strong> <?php echo htmlspecialchars($data['languages'] ?? ''); ?></p>
                    <p><strong>Hobbies:</strong> <?php echo htmlspecialchars($data['hobbies'] ?? ''); ?></p>
                </div>
            </section>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Address</h2>
                <p class="mt-2"><?php echo htmlspecialchars($data['address'] ?? ''); ?></p>
            </section>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Experience</h2>
                <div class="mt-2 space-y-3">
                    <?php foreach ($data['experience'] ?? [] as $exp): ?>
                        <div class="flex justify-between">
                            <p><strong><?php echo htmlspecialchars($exp['title'] ?? ''); ?></strong> - <?php echo htmlspecialchars($exp['company'] ?? ''); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($exp['years'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($data['experience'])): ?>
                        <div class="flex justify-between">
                            <p><strong>Software Engineer</strong> - ABC Corp</p>
                            <p class="text-gray-600">2020 - Present</p>
                        </div>
                        <p class="text-sm text-gray-600">Developed web applications using React & Node.js.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Projects</h2>
                <div class="mt-2 space-y-3">
                    <?php foreach ($data['projects'] ?? [] as $project): ?>
                        <p><?php echo htmlspecialchars($project); ?></p>
                    <?php endforeach; ?>
                    <?php if (empty($data['projects'])): ?>
                        <p><strong>Portfolio Website</strong> - A personal portfolio built with HTML, CSS, and JavaScript.</p>
                        <p><strong>Task Manager App</strong> - A web app developed using React and Node.js for task tracking.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Education</h2>
                <div class="mt-2 space-y-3">
                    <?php foreach ($data['education'] ?? [] as $edu): ?>
                        <div class="flex justify-between">
                            <p><strong><?php echo htmlspecialchars($edu['degree'] ?? ''); ?></strong> - <?php echo htmlspecialchars($edu['school'] ?? ''); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($edu['year'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($data['education'])): ?>
                        <div class="flex justify-between">
                            <p><strong>Bachelor of Computer Science</strong> - ABC University</p>
                            <p class="text-gray-600">2015 - 2019</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <section class="mt-6">
                <h2 class="text-xl font-semibold border-b pb-1">Skills</h2>
                <ul class="mt-2 grid grid-cols-2 list-disc ml-6 gap-y-1">
                    <?php foreach ($data['skills'] ?? [] as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                    <?php if (empty($data['skills'])): ?>
                        <li>JavaScript</li>
                        <li>React</li>
                        <li>Node.js</li>
                        <li>HTML & CSS</li>
                        <li>Tailwind CSS</li>
                        <li>Python & Django</li>
                    <?php endif; ?>
                </ul>
            </section>
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