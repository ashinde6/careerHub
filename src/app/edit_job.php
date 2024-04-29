<?php
include('header.php');

require 'connect-db.php';

// Fetch skills from the database
$stmt_skills = $db->query("SELECT * FROM Skills");
$skill_options = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);

// Fetch benefits from the database
$stmt_benefits = $db->query("SELECT * FROM Benefits");
$benefits_options = $stmt_benefits->fetchAll(PDO::FETCH_ASSOC);

// Fetch work types from the database
$stmt_worktype = $db->query("SELECT distinct work_type FROM Job");
$worktype_options = $stmt_worktype->fetchAll(PDO::FETCH_ASSOC);

// Fetch industries from the database
$stmt_industry = $db->query("SELECT distinct industry_name FROM Industry");
$industries = $stmt_industry->fetchAll(PDO::FETCH_ASSOC);

// Fetch salaries from the database
$stmt_salaries = $db->query("SELECT salary_id, min_salary, max_salary, pay_period, currency FROM Salary");
$salaries = $stmt_salaries->fetchAll(PDO::FETCH_ASSOC);
   
$job_id = $_GET['id'];
// Fetch job information from the database using $job_id
$stmt = $db->prepare("
SELECT j.job_name, j.work_type, j.description_id, c.name AS company_name, i.industry_name, l.description, s.min_salary, s.max_salary
FROM Job j
JOIN Company c ON j.company_id = c.company_id
JOIN Description l ON c.description_id = l.description_id
JOIN Industry i ON j.industry_id = i.industry_id
JOIN Salary s ON j.salary_id = s.salary_id
WHERE j.job_id = ?
");
$stmt->execute([$job_id]);
$job_info = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_skills = $db->prepare("
SELECT skill_name
FROM Requires
JOIN Skills ON Requires.skills_id = Skills.skills_id
WHERE job_id = ?
");

$stmt_skills->execute([$job_id]);
$existing_skills = $stmt_skills->fetchAll(PDO::FETCH_COLUMN);

// Fetch existing offers associated with the job
$stmt_offers = $db->prepare("
SELECT type
FROM Offers
JOIN Benefits ON Offers.benefits_id = Benefits.benefits_id
WHERE job_id = ?
");

$stmt_offers->execute([$job_id]);
$existing_offers = $stmt_offers->fetchAll(PDO::FETCH_COLUMN);

$stmt_posting = $db->prepare("
SELECT application_url, job_posting_url
FROM Posting
WHERE job_id = ?
");

try {
    $stmt_posting->execute([$job_id]);
    $posting_info = $stmt_posting->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$application_url = $posting_info['application_url'];
$job_posting_url = $posting_info['job_posting_url'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // if(isset($_POST['delete'])) {
        // Delete job offers associated with the job
        $stmt_delete_offers = $db->prepare("DELETE FROM Offers WHERE job_id = ?");
        $stmt_delete_offers->execute([$job_id]);

        // Delete skill requirements associated with the job
        $stmt_delete_requires = $db->prepare("DELETE FROM Requires WHERE job_id = ?");
        $stmt_delete_requires->execute([$job_id]);

        // Delete the posting
        $stmt_delete_posting = $db->prepare("DELETE FROM Posting WHERE job_id = ?");
        $stmt_delete_posting->execute([$job_id]);

        // Delete the job
        $stmt_delete_job = $db->prepare("DELETE FROM Job WHERE job_id = ?");
        $stmt_delete_job->execute([$job_id]);

        
    // } else {
    //     $job_name = $_POST['job_name'];
    //     $work_type = $_POST['work_type'];
    //     $industry = $_POST['industry'];
    //     $description = $_POST['description'];
    //     $min_salary = $_POST['min_salary'];
    //     $max_salary = $_POST['max_salary'];
    //     $salary_id = $_POST['salary'];
    //     $application_url = $_POST['application_url'];
    //     $job_posting_url = $_POST['job_posting_url'];
    //     $benefits = isset($_POST['benefits']) ? $_POST['benefits'] : [];
    //     $skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    
    //     // Update job information in the database
    //     $stmt_update_job = $db->prepare("
    //         UPDATE Job
    //         SET job_name = ?, work_type = ?, industry_id = ?
    //         WHERE job_id = ?
    //     ");
    //     $stmt_update_job->execute([$job_name, $work_type, $industry, $job_id]);
    
    //     // Update description information in the database
    //     $stmt_update_description = $db->prepare("
    //         UPDATE Description
    //         SET description = ?
    //         WHERE description_id = ?
    //     ");
    //     $stmt_update_description->execute([$description, $job_info['description_id']]);
    
    //     // Update salary information in the database
    //     $stmt_update_salary = $db->prepare("
    //         UPDATE Salary
    //         SET min_salary = ?, max_salary = ?
    //         WHERE salary_id = ?
    //     ");
    //     $stmt_update_salary->execute([$min_salary, $max_salary, $salary_id]);
    
    //     // Update job posting information in the database
    //     $stmt_update_posting = $db->prepare("
    //         UPDATE Posting
    //         SET application_url = ?, job_posting_url = ?
    //         WHERE job_id = ?
    //     ");
    //     $stmt_update_posting->execute([$application_url, $job_posting_url, $job_id]);
    
    //     // Update benefits associated with the job
    //     $stmt_delete_offers = $db->prepare("DELETE FROM Offers WHERE job_id = ?");
    //     $stmt_delete_offers->execute([$job_id]);
    
    //     foreach ($benefits as $benefit) {
    //         $benefit_id = getOrInsertID($db, "Benefits", "type", $benefit, "benefits_id");
    //         $stmt_insert_offer = $db->prepare("INSERT INTO Offers (job_id, benefits_id) VALUES (?, ?)");
    //         $stmt_insert_offer->execute([$job_id, $benefit_id]);
    //     }
    
    //     // Update skills associated with the job
    //     $stmt_delete_requires = $db->prepare("DELETE FROM Requires WHERE job_id = ?");
    //     $stmt_delete_requires->execute([$job_id]);
    
    //     foreach ($skills as $skill) {
    //         $skill_id = getOrInsertID($db, "Skills", "skill_name", $skill, "skills_id");
    //         $stmt_insert_require = $db->prepare("INSERT INTO Requires (job_id, skills_id) VALUES (?, ?)");
    //         $stmt_insert_require->execute([$job_id, $skill_id]);
    //     }

    // }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
</head>
<body>
    <form action="home.php" method="post">
        <input type="hidden" id="job_id" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
        
        <label for="job_name">Job Name:</label><br>
        <input type="text" id="job_name" name="job_name" value="<?php echo htmlspecialchars($job_info['job_name']); ?>" required><br>
        
        <label for="work_type">Work Type:</label><br>
<select id="work_type" name="work_type">
    <?php foreach ($worktype_options as $type): ?>
        <option value="<?php echo htmlspecialchars($type['work_type']); ?>" <?php echo ($type['work_type'] == $job_info['work_type']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['work_type']); ?></option>
    <?php endforeach; ?>
</select><br>

<label for="industry">Industry:</label><br>
<select id="industry" name="industry">
    <?php foreach ($industries as $industry): ?>
        <option value="<?php echo htmlspecialchars($industry['industry_name']); ?>" <?php echo ($industry['industry_name'] == $job_info['industry_name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($industry['industry_name']); ?></option>
    <?php endforeach; ?>
</select><br>

<label for="description">Description:</label><br>
<input type="text" id="description" name="description" value="<?php echo htmlspecialchars($job_info['description']); ?>" ><br>

<label for="salary">Salary:</label><br>
<select id="salary" name="salary">
    <?php foreach ($salaries as $salary): ?>
        <?php 
        $optionText = htmlspecialchars($salary['min_salary']) . '-' . htmlspecialchars($salary['max_salary']) . ' ' . htmlspecialchars($salary['pay_period']) . ' (' . htmlspecialchars($salary['currency']) . ')';
        $selected = ($salary['salary_id'] == $job_info['salary_id']) ? 'selected' : ''; // Check if it's the default value
        ?>
        <option value="<?php echo htmlspecialchars($salary['salary_id']); ?>" <?php echo $selected; ?>><?php echo $optionText; ?></option>
    <?php endforeach; ?>
</select><br>

<label for="application_url">Application URL:</label><br>
<input type="text" id="application_url" name="application_url" value="<?php echo htmlspecialchars($application_url) ?>"><br>

<label for="job_posting_url">Job Posting URL:</label><br>
<input type="text" id="job_posting_url" name="job_posting_url" value="<?php echo htmlspecialchars($job_posting_url) ?>"><br>

        
        <button type="submit">Save</button>
        <button onclick="window.location.href='home.php';">Cancel</button>
    </form>
    <form action="home.php" method="post">
        <input type="hidden" id="job_id" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
        <button type="submit" name="delete">Delete</button>
    </form>

</body>
</html>
