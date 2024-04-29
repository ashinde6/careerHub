<?php
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
    SELECT skills_name
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file here
    include "db_connection.php";

    // Get form data
    $job_name = $_POST["job_name"];
    $work_type = $_POST["work_type"];
    $industry = $_POST["industry"];
    $description = $_POST["description"];
    $salary = $_POST["salary"];
    $application_url = $_POST["application_url"];
    $job_posting_url = $_POST["job_posting_url"];
    $benefits = $_POST["benefits"];
    $skills = $_POST["skills"];

    // Fetch industry ID if it exists, or insert and get ID
    $industry_id = getOrInsertID($conn, "Industry", "name", $industry);

    // Fetch description ID if it exists, or insert and get ID
    $description_id = getOrInsertID($conn, "Description", "description", $description);

    // Fetch salary ID if it exists, or insert and get ID
    $salary_id = getOrInsertID($conn, "Salary", "currency", $salary);

    // SQL query to insert new job listing
    $stmt = $conn->prepare("INSERT INTO Job (job_name, work_type, industry_id, description_id, salary_id, company_id)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $job_name, $work_type, $industry_id, $description_id, $salary_id, $company_id);
    $stmt->execute();
    
    // Fetch the job ID of the newly inserted job
    $job_id = $conn->insert_id;

    // Insert the posting for the job
    $stmt = $conn->prepare("INSERT INTO Posting (job_id, application_url, job_posting_url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $job_id, $application_url, $job_posting_url);
    $stmt->execute();

    // Insert benefits for the job
    foreach ($benefits as $benefit) {
        $benefit_id = getOrInsertID($conn, "Benefits", "type", $benefit);
        $stmt = $conn->prepare("INSERT INTO Offers (job_id, benefits_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $job_id, $benefit_id);
        $stmt->execute();
    }

    // Insert skills for the job
    foreach ($skills as $skill) {
        $skill_id = getOrInsertID($conn, "Skills", "skills_name", $skill);
        $stmt = $conn->prepare("INSERT INTO Requires (job_id, skills_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $job_id, $skill_id);
        $stmt->execute();
    }

    // Redirect back to the previous page or any other page as needed
    header("Location: previous_page.php");
    exit();
}

// Function to get ID if exists, else insert and get ID
function getOrInsertID($conn, $table, $column, $value) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $column = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Value does not exist, insert into table
        $stmt = $conn->prepare("INSERT INTO $table ($column) VALUES (?)");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        return $conn->insert_id; // Get the auto-generated ID
    } else {
        // Value exists, fetch its ID
        $row = $result->fetch_assoc();
        return $row["$table"."_id"];
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
</head>
<body>
    <form action="submit_job.php" method="post">
    <?php if (!empty($job_info)): ?>
        <label for="job_name">Job Name:</label><br>
        <input type="text" id="job_name" name="job_name" value="<?php echo htmlspecialchars($job_info['job_name']); ?>" required><br>
        
        <label for="work_type">Work Type:</label><br>
        <input type="text" id="work_type" name="work_type" value="<?php echo htmlspecialchars($job_info['work_type']); ?>"><br>
        
        <label for="industry">Industry:</label><br>
        <input type="text" id="industry" name="industry" value="<?php echo htmlspecialchars($job_info['industry_name']); ?>"><br>
        
        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($job_info['description']); ?>"><br>
        
        <label for="salary">Salary:</label><br>
        <input type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($job_info['min_salary']) . '-' . htmlspecialchars($job_info['max_salary']); ?>"><br>

        <label for="application_url">Application URL:</label><br>
        <input type="text" id="application_url" name="application_url" value="<?php echo htmlspecialchars($job_info['application_url']); ?>"><br>
        
        <label for="job_posting_url">Job Posting URL:</label><br>
        <input type="text" id="job_posting_url" name="job_posting_url" value="<?php echo htmlspecialchars($job_info['job_posting_url']); ?>"><br>
       
        <label for="benefits">Benefits:</label><br>
        <select multiple id="benefits" name="benefits[]">
            <?php foreach ($existing_offers as $offer): ?>
                <option value="<?php echo htmlspecialchars($offer); ?>" selected><?php echo htmlspecialchars($offer); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="skills">Skills:</label><br>
        <select multiple id="skills" name="skills[]">
            <?php foreach ($existing_skills as $skill): ?>
                <option value="<?php echo htmlspecialchars($skill); ?>" selected><?php echo htmlspecialchars($skill); ?></option>
            <?php endforeach; ?>
        </select><br>
        
     <?php else: ?>
        <label for="job_name">Job Name:</label><br>
        <input type="text" id="job_name" name="job_name" required><br>
        
        <label for="work_type">Work Type:</label><br>
        <input type="text" id="work_type" name="work_type"><br>
        
        <label for="industry">Industry:</label><br>
        <input type="text" id="industry" name="industry"><br>
        
        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br>
        
        <label for="salary">Salary:</label><br>
        <input type="text" id="salary" name="salary"><br>

        <label for="application_url">Application URL:</label><br>
        <input type="text" id="application_url" name="application_url"><br>
        
        <label for="job_posting_url">Job Posting URL:</label><br>
        <input type="text" id="job_posting_url" name="job_posting_url"><br>
        
        <label for="benefits">Benefits:</label><br>
        <select multiple id="benefits" name="benefits[]">
            <?php foreach ($existing_offers as $offer): ?>
                <option value="<?php echo htmlspecialchars($offer); ?>" selected><?php echo htmlspecialchars($offer); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="skills">Skills:</label><br>
        <select multiple id="skills" name="skills[]">
            <?php foreach ($existing_skills as $skill): ?>
                <option value="<?php echo htmlspecialchars($skill); ?>" selected><?php echo htmlspecialchars($skill); ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="button" onclick="window.location.href='home.php';">Save</button>
        <button type="button" onclick="window.location.href='home.php';">Cancel</button>
        <button type="button" onclick="window.location.href='home.php';">Delete</button>
    </form>
    <?php endif; ?>
</body>
</html>