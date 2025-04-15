<?php
require 'function.class.php';
require '../src/database.class.php';

// [Your existing PHP logic from template1.php, unchanged]
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

// Fetch related data with limits
$experience = $education = $skills = $projects = $certificates = $achievements = [];

$stmt = $db->prepare("SELECT * FROM resume_experience WHERE resume_id = ? LIMIT 3");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$experience = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $db->prepare("SELECT * FROM resume_education WHERE resume_id = ? LIMIT 2");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $db->prepare("SELECT skill FROM resume_skills WHERE resume_id = ? LIMIT 5");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row['skill'];
}
$stmt->close();

$stmt = $db->prepare("SELECT project FROM resume_projects WHERE resume_id = ? LIMIT 3");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row['project'];
}
$stmt->close();

$stmt = $db->prepare("SELECT certificate FROM resume_certificates WHERE resume_id = ? LIMIT 3");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$certificates = [];
while ($row = $result->fetch_assoc()) {
    $certificates[] = $row['certificate'];
}
$stmt->close();

$stmt = $db->prepare("SELECT achievement FROM resume_achievements WHERE resume_id = ? LIMIT 3");
$stmt->bind_param("i", $resumeId);
$stmt->execute();
$result = $stmt->get_result();
$achievements = [];
while ($row = $result->fetch_assoc()) {
    $achievements[] = $row['achievement'];
}
$stmt->close();

// Count total items for truncation indicators
$total_skills = count($skills);
$total_projects = count($projects);
$total_certificates = count($certificates);
$total_achievements = count($achievements);

// Limit arrays
$skills = array_slice($skills, 0, 5);
$projects = array_slice($projects, 0, 3);
$certificates = array_slice($certificates, 0, 3);
$achievements = array_slice($achievements, 0, 3);

// Combine data
$data['experience'] = $experience;
$data['education'] = $education;
$data['skills'] = $skills;
$data['projects'] = $projects;
$data['certificates'] = $certificates;
$data['achievements'] = $achievements;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Template 4 | Resume Builder</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap');
        #resumeContent {
            width: 240mm;
            max-height: 360mm;
            box-sizing: border-box;
            overflow: hidden;
            font-size: 16px;
            padding: 15mm;
            font-family: 'Open Sans', sans-serif;
        }
        @media print {
            body * { visibility: hidden; }
            #resumeContent, #resumeContent * { visibility: visible; }
            #resumeContent {
                position: absolute;
                top: 0;
                left: 0;
                width: 240mm;
                max-height: 360mm;
                padding: 15mm;
                box-shadow: none;
                border: none;
                overflow: hidden;
            }
        }
        .no-break { page-break-inside: avoid; }
        .section-break { page-break-before: always; }
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
        h1 { font-size: 3rem; font-weight: bold; line-height: 1.2; }
        h2 { font-size: 1.8rem; font-weight: bold; line-height: 1.3; color: #1a3c5e; }
        p, li { line-height: 1.6; margin-bottom: 0.5rem; }
        section { margin-top: 2rem; }
    </style>
</head>
<body class="animated-background flex justify-center py-10">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="w-full max-w-4xl">
        <?php if ($mode === 'view'): ?>
            <div class="bg-white p-4 mb-4 shadow-lg w-[240mm] rounded-lg flex justify-between items-center">
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
        <div id="resumeContent" class="bg-white shadow-lg">
            <header class="text-center border-b-2 border-gray-800 pb-4 no-break">
                <?php if (!empty($data['full_name'])): ?>
                    <h1 class="text-4xl font-bold"><?php echo htmlspecialchars($data['full_name']); ?></h1>
                <?php endif; ?>
                <?php if (!empty($data['experience'][0]['title'])): ?>
                    <p class="text-lg text-gray-600"><?php echo htmlspecialchars($data['experience'][0]['title']); ?></p>
                <?php endif; ?>
                <div class="mt-2 text-gray-600">
                    <?php if (!empty($data['email'])): ?>
                        <span><?php echo htmlspecialchars($data['email']); ?> | </span>
                    <?php endif; ?>
                    <?php if (!empty($data['mobile'])): ?>
                        <span><?php echo htmlspecialchars($data['mobile']); ?> | </span>
                    <?php endif; ?>
                    <?php if (!empty($data['linkedin'])): ?>
                        <span><?php echo htmlspecialchars($data['linkedin']); ?></span>
                    <?php endif; ?>
                </div>
            </header>
            <?php if (!empty($data['address']) || !empty($data['nationality']) || !empty($data['marital_status']) || !empty($data['languages']) || !empty($data['hobbies'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Executive Summary</h2>
                    <div class="mt-2">
                        <?php if (!empty($data['address'])): ?>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($data['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['nationality'])): ?>
                            <p><strong>Nationality:</strong> <?php echo htmlspecialchars($data['nationality']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['languages'])): ?>
                            <p><strong>Languages:</strong> <?php echo htmlspecialchars($data['languages']); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['experience'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Leadership Experience</h2>
                    <div class="mt-2 space-y-4">
                        <?php foreach ($data['experience'] as $exp): ?>
                            <?php if (!empty($exp['title']) || !empty($exp['company']) || !empty($exp['years'])): ?>
                                <div>
                                    <p class="font-bold text-lg"><?php echo htmlspecialchars($exp['title']); ?> - <?php echo htmlspecialchars($exp['company']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($exp['years']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['education'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Education</h2>
                    <div class="mt-2 space-y-3">
                        <?php foreach ($data['education'] as $edu): ?>
                            <?php if (!empty($edu['degree']) || !empty($edu['school']) || !empty($edu['year'])): ?>
                                <div>
                                    <p class="font-bold"><?php echo htmlspecialchars($edu['degree']); ?> - <?php echo htmlspecialchars($edu['school']); ?></p>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($edu['year']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['skills'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Core Competencies</h2>
                    <ul class="mt-2 list-disc ml-5" data-section="skills">
                        <?php foreach ($data['skills'] as $skill): ?>
                            <?php if (!empty($skill)): ?>
                                <li><?php echo htmlspecialchars($skill); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($total_skills > 5): ?>
                            <li class="text-gray-500">... and <?php echo $total_skills - 5; ?> more</li>
                        <?php endif; ?>
                    </ul>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['projects'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Key Projects</h2>
                    <div class="mt-2 space-y-2" data-section="projects">
                        <?php foreach ($data['projects'] as $project): ?>
                            <?php if (!empty($project)): ?>
                                <p><?php echo htmlspecialchars($project); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($total_projects > 3): ?>
                            <p class="text-gray-500">... and <?php echo $total_projects - 3; ?> more</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['certificates'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Certifications</h2>
                    <div class="mt-2 space-y-2" data-section="certificates">
                        <?php foreach ($data['certificates'] as $certificate): ?>
                            <?php if (!empty($certificate)): ?>
                                <p><?php echo htmlspecialchars($certificate); ?></p>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        <?php if ($total_certificates > 3): ?>
                            <p class="text-gray-500">... and <?php echo $total_certificates - 3; ?> more</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['achievements'])): ?>
                <section class="no-break">
                    <h2 class="border-b-2 border-gray-800 pb-1">Achievements</h2>
                    <div class="mt-2 space-y-2" data-section="achievements">
                        <?php foreach ($data['achievements'] as $achievement): ?>
                            <?php if (!empty($achievement)): ?>
                                <p><?php echo htmlspecialchars($achievement); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($total_achievements > 3): ?>
                            <p class="text-gray-500">... and <?php echo $total_achievements - 3; ?> more</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($mode === 'view'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // [Your existing JavaScript from template1.php, unchanged]
            const resumeContent = document.getElementById('resumeContent');
            const fontSelect = document.getElementById('fontSelect');
            const printBtn = document.getElementById('printBtn');
            const downloadBtn = document.getElementById('downloadBtn');
            const shareBtn = document.getElementById('shareBtn');
            const atsCheckBtn = document.getElementById('atsCheckBtn');

            function adjustContentToFit() {
                const maxHeight = 360 * 3.78;
                let currentHeight = resumeContent.offsetHeight;

                if (currentHeight > maxHeight) {
                    let fontSize = parseFloat(window.getComputedStyle(resumeContent).fontSize);
                    resumeContent.style.fontSize = `${fontSize * 0.95}px`;
                    currentHeight = resumeContent.offsetHeight;

                    if (currentHeight > maxHeight) {
                        const lists = ['skills', 'projects', 'certificates', 'achievements', 'experience', 'education'];
                        lists.forEach(section => {
                            const items = resumeContent.querySelectorAll(`[data-section="${section}"] li, [data-section="${section}"] div`);
                            if (items.length > 2) {
                                for (let i = 2; i < items.length; i++) {
                                    items[i].style.display = 'none';
                                }
                                const more = document.createElement('p');
                                more.className = 'text-gray-500 text-sm';
                                more.textContent = `... and ${items.length - 2} more`;
                                items[1].parentNode.appendChild(more);
                            }
                        });
                    }

                    currentHeight = resumeContent.offsetHeight;
                    if (currentHeight > maxHeight) {
                        resumeContent.style.transform = `scale(${maxHeight / currentHeight})`;
                        resumeContent.style.transformOrigin = 'top left';
                        resumeContent.style.height = '360mm';
                    }
                }
            }

            window.addEventListener('load', adjustContentToFit);

            fontSelect.addEventListener('change', function() {
                resumeContent.style.fontFamily = this.value;
                adjustContentToFit();
            });

            printBtn.addEventListener('click', function() {
                adjustContentToFit();
                window.print();
            });

            downloadBtn.addEventListener('click', function () {
                adjustContentToFit();
                const element = resumeContent.cloneNode(true);
                const fontFamily = fontSelect.value || 'Times New Roman';
                element.style.fontFamily = fontFamily;
                element.style.width = '190mm';
                element.style.height = 'auto';
                element.style.margin = '0';
                element.style.backgroundColor = 'white';
                element.style.padding = '5mm';
                element.style.boxSizing = 'border-box';
                element.style.display = 'block';
                element.style.boxShadow = 'none';
                element.classList.remove('shadow-lg');

                function generatePdf() {
                    const opt = {
                        margin: [5, 5, 5, 5],
                        filename: 'resume.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true, logging: false },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak: { mode: ['avoid-all', 'css', 'legacy'], before: '.page-break-before', after: '.page-break-after' }
                    };

                    html2pdf()
                        .set(opt)
                        .from(element)
                        .toPdf()
                        .get('pdf')
                        .then((pdf) => {
                            const totalPages = pdf.internal.getNumberOfPages();
                            for (let i = 1; i <= totalPages; i++) {
                                pdf.setPage(i);
                            }
                        })
                        .save();
                }

                const contentHeightPx = element.offsetHeight;
                const maxHeightPxSinglePage = 277 * 3.78;
                if (contentHeightPx > maxHeightPxSinglePage * 4) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Content is unusually long and may result in many pages. Consider editing for brevity.',
                        icon: 'warning',
                        confirmButtonText: 'Proceed',
                        showCancelButton: true,
                        cancelButtonText: 'Edit Resume'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            generatePdf();
                        }
                    });
                } else {
                    generatePdf();
                }
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

                const keywords = [
                    'software', 'developer', 'engineer', 'project', 'team', 'management', 
                    'certificate', 'achievement', 'design', 'development', 'analysis', 
                    'programming', 'leadership', 'communication', 'python', 'java', 
                    'javascript', 'sql', 'cloud', 'aws'
                ];
                let keywordCount = 0;
                keywords.forEach(keyword => {
                    const regex = new RegExp(`\\b${keyword}\\b`, 'g');
                    keywordCount += (content.match(regex) || []).length;
                });
                const keywordScore = Math.min((keywordCount / 10) * 30, 30);
                score += keywordScore;
                if (keywordCount < 5) {
                    feedback.push('Include more job-specific keywords (e.g., software, project, python).');
                } else if (keywordCount > 15) {
                    feedback.push('Avoid keyword stuffing; ensure keywords are relevant.');
                }

                const sections = ['experience', 'education', 'skills', 'projects', 'certificates'];
                let sectionScore = 0;
                sections.forEach(section => {
                    if (content.includes(section)) {
                        sectionScore += 5;
                    } else {
                        feedback.push(`Add a "${section.charAt(0).toUpperCase() + section.slice(1)}" section.`);
                    }
                });
                score += sectionScore;

                let formattingScore = 15;
                const fontFamily = window.getComputedStyle(resumeContent).fontFamily.toLowerCase();
                const standardFonts = ['roboto', 'open sans', 'lora', 'times new roman', 'arial', 'helvetica'];
                if (!standardFonts.some(font => fontFamily.includes(font))) {
                    formattingScore -= 5;
                    feedback.push('Use standard fonts like Roboto or Times New Roman for ATS compatibility.');
                }
                if (resumeContent.querySelector('table')) {
                    formattingScore -= 5;
                    feedback.push('Avoid tables; they may not parse correctly in ATS.');
                }
                if (content.includes('•') || content.includes('★')) {
                    formattingScore -= 5;
                    feedback.push('Minimize special characters; use simple bullets or hyphens.');
                }
                score += formattingScore;

                const words = content.split(/\s+/).filter(word => word.length > 1);
                const wordCount = words.length;
                let lengthScore = 0;
                if (wordCount >= 100 && wordCount <= 600) {
                    lengthScore = 15;
                } else if (wordCount < 100) {
                    lengthScore = 5;
                    feedback.push('Resume is too short; aim for 100–600 words.');
                } else if (wordCount > 600 && wordCount <= 800) {
                    lengthScore = 10;
                    feedback.push('Resume is slightly long; try to keep it concise.');
                } else {
                    lengthScore = 5;
                    feedback.push('Resume is too long; reduce to under 600 words for better ATS parsing.');
                }
                score += lengthScore;

                let contactScore = 0;
                if (content.includes('@') && content.includes('.')) {
                    contactScore += 5;
                } else {
                    feedback.push('Include a valid email address.');
                }
                if (content.match(/\b\d{10}\b|\(\d{3}\)\s?\d{3}-\d{4}/)) {
                    contactScore += 5;
                } else {
                    feedback.push('Include a phone number.');
                }
                score += contactScore;

                let readabilityScore = 5;
                const sentences = content.split(/[.!?]+/).filter(s => s.trim().length > 0);
                const longSentences = sentences.filter(s => s.split(/\s+/).length > 25).length;
                if (longSentences / sentences.length > 0.2) {
                    readabilityScore -= 3;
                    feedback.push('Shorten sentences for better readability (most under 25 words).');
                }
                score += readabilityScore;

                score = Math.round(score);

                Swal.fire({
                    title: `ATS Score: ${score}%`,
                    html: feedback.length > 0 ? 
                        `<p><strong>Feedback:</strong></p><ul class="list-disc pl-5">${feedback.map(f => `<li>${f}</li>`).join('')}</ul>` : 
                        '<p>Your resume is highly ATS-compatible!</p>',
                    icon: score >= 85 ? 'success' : score >= 60 ? 'warning' : 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>