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
    <title>Resume Template 2 | Resume Builder</title>
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
            font-size: 17px;
            padding: 14mm;
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
                width: 240mm;
                max-height: 360mm;
                padding: 14mm;
                box-shadow: none;
                border: none;
                overflow: hidden;
            }
        }
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
        h1 { font-size: 3.5rem; font-weight: bold; line-height: 1.2; }
        h2 { font-size: 1.75rem; line-height: 1.3; }
        p, li { line-height: 1.6; margin-bottom: 0.4rem; }
        section { margin-top: 1.5rem; }
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
        <div id="resumeContent" class="border border-gray-300 bg-white shadow-lg p-6 rounded-lg">
            <div class="text-center border-b pb-2">
                <?php if (!empty($data['full_name'])): ?>
                    <h1 class="text-xl"><strong><?php echo htmlspecialchars($data['full_name']); ?></strong></h1>
                <?php endif; ?>
                <?php if (!empty($data['experience'][0]['title'])): ?>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($data['experience'][0]['title']); ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($data['email']) || !empty($data['mobile']) || !empty($data['linkedin']) || !empty($data['github']) || !empty($data['address']) || !empty($data['nationality']) || !empty($data['marital_status'])): ?>
                <section>
                    <h2 class="border-b pb-1">Personal Information</h2>
                    <div class="mt-1 ">
                        <?php if (!empty($data['email'])): ?>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['mobile'])): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($data['mobile']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['linkedin'])): ?>
                            <p><strong>LinkedIn:</strong> <?php echo htmlspecialchars($data['linkedin']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['github'])): ?>
                            <p><strong>GitHub:</strong> <?php echo htmlspecialchars($data['github']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['address'])): ?>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($data['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['nationality'])): ?>
                            <p><strong>Nationality:</strong> <?php echo htmlspecialchars($data['nationality']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($data['marital_status'])): ?>
                            <p><strong>Marital Status:</strong> <?php echo htmlspecialchars($data['marital_status']); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['skills'])): ?>
                <section>
                    <h2 class="border-b pb-1">Skills</h2>
                    <ul class="mt-1 list-disc pl-4 " data-section="skills">
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
            <?php if (!empty($data['experience'])): ?>
                <section>
                    <h2 class="border-b pb-1">Experience</h2>
                    <div class="mt-1 " data-section="experience">
                        <?php foreach ($data['experience'] as $exp): ?>
                            <?php if (!empty($exp['title']) || !empty($exp['company']) || !empty($exp['years'])): ?>
                                <div>
                                    <p>
                                        <?php if (!empty($exp['title'])): ?>
                                            <strong><?php echo htmlspecialchars($exp['title']); ?></strong>
                                        <?php endif; ?>
                                        <?php if (!empty($exp['company'])): ?>
                                            - <?php echo htmlspecialchars($exp['company']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!empty($exp['years'])): ?>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($exp['years']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['projects'])): ?>
                <section>
                    <h2 class="border-b pb-1">Projects</h2>
                    <div class="mt-1 " data-section="projects">
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
                <section>
                    <h2 class="border-b pb-1">Certificates</h2>
                    <div class="mt-1 " data-section="certificates">
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
                <section>
                    <h2 class="border-b pb-1">Achievements</h2>
                    <div class="mt-1 " data-section="achievements">
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
            <?php if (!empty($data['education'])): ?>
                <section>
                    <h2 class="border-b pb-1">Education</h2>
                    <div class="mt-1 " data-section="education">
                        <?php foreach ($data['education'] as $edu): ?>
                            <?php if (!empty($edu['degree']) || !empty($edu['school']) || !empty($edu['year'])): ?>
                                <div>
                                    <p>
                                        <?php if (!empty($edu['degree'])): ?>
                                            <strong><?php echo htmlspecialchars($edu['degree']); ?></strong>
                                        <?php endif; ?>
                                        <?php if (!empty($edu['school'])): ?>
                                            - <?php echo htmlspecialchars($edu['school']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!empty($edu['year'])): ?>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($edu['year']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['languages'])): ?>
                <section>
                    <h2 class="border-b pb-1">Languages Known</h2>
                    <p class="mt-1 "><?php echo htmlspecialchars($data['languages']); ?></p>
                </section>
            <?php endif; ?>
            <?php if (!empty($data['hobbies'])): ?>
                <section>
                    <h2 class="border-b pb-1">Hobbies</h2>
                    <p class="mt-1 "><?php echo htmlspecialchars($data['hobbies']); ?></p>
                </section>
            <?php endif; ?>
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

    function adjustContentToFit() {
        const maxHeight = 360 * 3.78; /* 360mm in pixels */
        let currentHeight = resumeContent.offsetHeight;

        if (currentHeight > maxHeight) {
            let fontSize = parseFloat(window.getComputedStyle(resumeContent).fontSize);
            resumeContent.style.fontSize = `${fontSize * 0.95}px`; /* More aggressive scaling from 0.95 */
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

    downloadBtn.addEventListener('click', function() {
    adjustContentToFit();
    const element = resumeContent.cloneNode(true);
    const fontFamily = fontSelect.value || 'Roboto';
    element.style.fontFamily = fontFamily;
    element.style.width = '190mm';
    element.style.height = 'auto';
    element.style.margin = '0';
    element.style.backgroundColor = 'white';
    // element.style.padding = '10mm';
    element.style.boxSizing = 'border-box';
    element.style.display = 'block';
    element.style.boxShadow = 'none';
    element.classList.remove('shadow-lg');

    // Warn if content may exceed two pages
    const maxHeightPxTwoPages = 2 * 277 * 3.78; // Two A4 pages (277mm usable height each)
    const contentHeightPx = element.offsetHeight;
    if (contentHeightPx > maxHeightPxTwoPages) {
        Swal.fire({
            title: 'Warning',
            text: 'Content may be too long for two pages. Some details might be cut off. Consider editing to fit.',
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

    function generatePdf() {
        const opt = {
            margin: [0, 0, 10, 0],
            filename: 'resume.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
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

        // Keyword Relevance (30%)
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

        // Section Completeness (25%)
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

        // Formatting (15%)
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

        // Content Length (15%)
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

        // Contact Information (10%)
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

        // Readability (5%)
        let readabilityScore = 5;
        const sentences = content.split(/[.!?]+/).filter(s => s.trim().length > 0);
        const longSentences = sentences.filter(s => s.split(/\s+/).length > 25).length;
        if (longSentences / sentences.length > 0.2) {
            readabilityScore -= 3;
            feedback.push('Shorten sentences for better readability (most under 25 words).');
        }
        score += readabilityScore;

        // Round score
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