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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $job_name = $_POST["job_name"];
    $work_type = $_POST["work_type"];
    $industry = $_POST["industry"];
    $description = $_POST["description"];
    $salary_id = $_POST["salary"];
    $application_url = $_POST["application_url"];
    $job_posting_url = $_POST["job_posting_url"];
    $benefits = isset($_POST["benefits"]) ? $_POST["benefits"] : [];
    $skills = isset($_POST["skills"]) ? $_POST["skills"] : [];
    $company_id = $_SESSION['company_id'];

    // Fetch industry ID if it exists, or insert and get ID
    $industry_id = getOrInsertID($db, "Industry", "industry_name", $industry, "industry_id");

    // Fetch description ID if it exists, or insert and get ID
    $description_id = getOrInsertID($db, "Description", "description", $description, "description_id");

    // SQL query to insert new job listing
    $stmt = $db->prepare("INSERT INTO Job (job_name, work_type, industry_id, description_id, salary_id, company_id)
    VALUES (:job_name, :work_type, :industry_id, :description_id, :salary_id, :company_id)");

    $stmt->bindParam(':job_name', $job_name);
    $stmt->bindParam(':work_type', $work_type);
    $stmt->bindParam(':industry_id', $industry_id);
    $stmt->bindParam(':description_id', $description_id);
    $stmt->bindParam(':salary_id', $salary_id);
    $stmt->bindParam(':company_id', $company_id);
    $stmt->execute();

    // Fetch the job ID of the newly inserted job
    $job_id = $db->lastInsertId();

    // Insert the posting for the job
    $stmt = $db->prepare("INSERT INTO Posting (job_id, application_url, job_posting_url) VALUES (:job_id, :application_url, :job_posting_url)");
    $stmt->bindParam(":job_id", $job_id, PDO::PARAM_INT);
    $stmt->bindParam(":application_url", $application_url, PDO::PARAM_STR);
    $stmt->bindParam(":job_posting_url", $job_posting_url, PDO::PARAM_STR);
    $stmt->execute();

    // Insert benefits for the job
    foreach ($benefits as $benefit) {
        $benefit_id = getOrInsertID($db, "Benefits", "type", $benefit, "benefits_id");
        $stmt = $db->prepare("INSERT INTO Offers (job_id, benefits_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $job_id, $benefit_id);
        $stmt->execute();
    }

    // Insert skills for the job
    foreach ($skills as $skill) {
        $skill_id = getOrInsertID($db, "Skills", "skill_name", $skill, "skills_id");
        $stmt = $db->prepare("INSERT INTO Requires (job_id, skills_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $job_id, $skill_id);
        $stmt->execute();
    }

    exit();
}

function getOrInsertID($conn, $table, $column, $value, $id_name) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $column = :value");
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 0) {
        // Value does not exist, insert into table
        $stmt = $conn->prepare("INSERT INTO $table ($column) VALUES (:value)");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $conn->lastInsertId(); // Get the auto-generated ID
    } else {
        // Value exists, fetch its ID
        return $result[0][$id_name];
    }
}

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
    <form action="home.php" method="post">
        <label for="job_name">Job Name:</label><br>
        <input type="text" id="job_name" name="job_name" required><br>
        
        <label for="work_type">Work Type:</label><br>
        <select id="work_type" name="work_type">
            <?php
            foreach ($worktype_options as $type) {
                echo "<option value='" . htmlspecialchars($type['work_type']) . "'>" . htmlspecialchars($type['work_type']) . "</option>";
            }
            ?>
        </select><br>

        <label for="industry">Industry:</label><br>
        <select id="industry" name="industry">
            <?php
            foreach ($industries as $industry) {
                echo "<option value='" . htmlspecialchars($industry['industry_name']) . "'>" . htmlspecialchars($industry['industry_name']) . "</option>";
            }
            ?>
        </select><br>

        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br>
        
        <label for="salary">Salary:</label><br>
        <select id="salary" name="salary">
            <?php
            foreach ($salaries as $salary) {
                $optionText = htmlspecialchars($salary['min_salary']) . '-' . htmlspecialchars($salary['max_salary']) . ' ' . htmlspecialchars($salary['pay_period']) . ' (' . htmlspecialchars($salary['currency']) . ')';
                echo "<option value='" . htmlspecialchars($salary['salary_id']) . "'>" . $optionText . "</option>";
            }
            ?>
        </select><br>

        <label for="application_url">Application URL:</label><br>
        <input type="text" id="application_url" name="application_url"><br>
        
        <label for="job_posting_url">Job Posting URL:</label><br>
        <input type="text" id="job_posting_url" name="job_posting_url"><br>
       
        <label for="skills">Skills:</label><br>
        <select id="skillsDropdown">
            <?php foreach ($skill_options as $skill): ?>
                <option value="<?php echo $skill['skill_name']; ?>"><?php echo $skill['skill_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="addSkill()">Add Skill</button><br>
        <ul id="selectedSkills" name=id="selectedSkills"></ul>

        <label for="benefits">Benefits:</label><br>
        <select id="benefitsDropdown">
            <?php foreach ($benefits_options as $benefit): ?>
                <option value="<?php echo $benefit['type']; ?>"><?php echo $benefit['type']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="addBenefit()">Add Benefit</button><br>
        <ul id="selectedBenefits" name="selectedBenefits"></ul>
        
        <button type="submit">Submit</button>
        <button onclick="window.location.href='home.php';">Cancel</button>
    </form>

    <script>
        // Function to add selected skill to the list
        function addSkill() {
            var dropdown = document.getElementById("skillsDropdown");
            var selectedSkill = dropdown.options[dropdown.selectedIndex].text;
            var list = document.getElementById("selectedSkills");
            var listItem = document.createElement("li");
            listItem.appendChild(document.createTextNode(selectedSkill));
            list.appendChild(listItem);
        }

        // Function to add selected benefit to the list
        function addBenefit() {
            var dropdown = document.getElementById("benefitsDropdown");
            var selectedBenefit = dropdown.options[dropdown.selectedIndex].text;
            var list = document.getElementById("selectedBenefits");
            var listItem = document.createElement("li");
            listItem.appendChild(document.createTextNode(selectedBenefit));
            list.appendChild(listItem);
        }
    </script>
</body>
</html>