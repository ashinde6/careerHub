<?php
include('header.php');

require 'connect-db.php';
    $job_id = $_GET['id'];
    // Fetch job information from the database using $job_id
    $stmt = $db->prepare("
    SELECT j.job_name, j.work_type, c.name AS company_name, i.industry_name, l.address_city, l.address_state, s.min_salary, s.max_salary
    FROM Job j
    JOIN Company c ON j.company_id = c.company_id
    JOIN Location l ON c.location_id = l.location_id
    JOIN Industry i ON j.industry_id = i.industry_id
    JOIN Salary s ON j.salary_id = s.salary_id
    WHERE j.job_id = ?
    ");
    $stmt->execute([$job_id]);

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

    $job_info = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Job Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
</head>
<body>
    <form action="edit_job.php" method="post">
        <label for="job_name">Job Name:</label><br>
        <input type="text" id="job_name" name="job_name" value="<?php echo htmlspecialchars($job_info['job_name']); ?>" required><br>
        
        <label for="work_type">Work Type:</label><br>
        <select id="work_type" name="work_type">
            <?php
            foreach ($worktype_options as $type) {
                $selected = ($type['work_type'] == $job_info['work_type']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($type['work_type']) . "' $selected>" . htmlspecialchars($type['work_type']) . "</option>";
            }
            ?>
        </select><br>

        <label for="industry">Industry:</label><br>
        <select id="industry" name="industry">
            <?php
            foreach ($industries as $industry) {
                $selected = ($industry['industry_name'] == $job_info['industry_name']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($industry['industry_name']) . "' $selected>" . htmlspecialchars($industry['industry_name']) . "</option>";
            }
            ?>
        </select><br>

        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($job_info['description']); ?>"><br>
        
        <label for="salary">Salary:</label><br>
        <select id="salary" name="salary">
            <?php
            foreach ($salaries as $salary) {
                $optionText = htmlspecialchars($salary['min_salary']) . '-' . htmlspecialchars($salary['max_salary']) . ' ' . htmlspecialchars($salary['pay_period']) . ' (' . htmlspecialchars($salary['currency']) . ')';
                $selected = ($salary['salary_id'] == $job_info['salary_id']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($salary['salary_id']) . "' $selected>" . $optionText . "</option>";
            }
            ?>
        </select><br>
        
        <label for="application_url">Application URL:</label><br>
        <input type="text" id="application_url" name="application_url" value="<?php echo htmlspecialchars($job_info['application_url']); ?>"><br>
        
        <label for="job_posting_url">Job Posting URL:</label><br>
        <input type="text" id="job_posting_url" name="job_posting_url" value="<?php echo htmlspecialchars($job_info['job_posting_url']); ?>"><br>
    
        <label for="skills">Skills:</label><br>
        <select id="skillsDropdown" multiple>
            <?php 
            foreach ($skills_options as $skill): 
                $selected = (in_array($skill['skill_name'], $existing_skills)) ? 'selected' : '';
            ?>
                <option value="<?php echo $skill['skill_name']; ?>" <?php echo $selected; ?>><?php echo $skill['skill_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="addSkill()">Add Skill</button><br>
        <ul id="selectedSkills">
            <?php 
            foreach ($existing_skills as $skill): 
            ?>
                <li><?php echo $skill; ?></li>
            <?php endforeach; ?>
        </ul>
        
        <label for="benefits">Benefits:</label><br>
        <select id="benefitsDropdown" multiple>
            <?php 
            foreach ($benefits_options as $benefit): 
                $selected = (in_array($benefit['type'], $existing_offers)) ? 'selected' : '';
            ?>
                <option value="<?php echo $benefit['type']; ?>" <?php echo $selected; ?>><?php echo $benefit['type']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="addBenefit()">Add Benefit</button><br>
        <ul id="selectedBenefits">
            <?php 
            foreach ($existing_offers as $offer): 
            ?>
                <li><?php echo $offer; ?></li>
            <?php endforeach; ?>
        </ul>

        <button type="submit">Save</button>
        <button type="button" onclick="window.location.href='home.php';">Cancel</button>
        <button type="button" onclick="window.location.href='home.php';">Delete</button>
    </form>
</body>
</html>