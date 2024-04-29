<?php
// Check if the form is submitted
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

    // Fetch industry ID if it exists, or insert and get ID
    $stmt = $conn->prepare("SELECT industry_id FROM Industry WHERE name = ?");
    $stmt->bind_param("s", $industry);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Industry does not exist, insert into Industry table
        $stmt = $conn->prepare("INSERT INTO Industry (name) VALUES (?)");
        $stmt->bind_param("s", $industry);
        $stmt->execute();
        $industry_id = $conn->insert_id; // Get the auto-generated industry ID
    } else {
        // Industry exists, fetch its ID
        $row = $result->fetch_assoc();
        $industry_id = $row["industry_id"];
    }

    // Fetch description ID if it exists, or insert and get ID
    $stmt = $conn->prepare("SELECT description_id FROM Description WHERE description = ?");
    $stmt->bind_param("s", $description);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Description does not exist, insert into Description table
        $stmt = $conn->prepare("INSERT INTO Description (description) VALUES (?)");
        $stmt->bind_param("s", $description);
        $stmt->execute();
        $description_id = $conn->insert_id; // Get the auto-generated description ID
    } else {
        // Description exists, fetch its ID
        $row = $result->fetch_assoc();
        $description_id = $row["description_id"];
    }

    // Fetch salary ID if it exists, or insert and get ID
    $stmt = $conn->prepare("SELECT salary_id FROM Salary WHERE currency = ?");
    $stmt->bind_param("s", $salary);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Salary does not exist, insert into Salary table
        $stmt = $conn->prepare("INSERT INTO Salary (currency) VALUES (?)");
        $stmt->bind_param("s", $salary);
        $stmt->execute();
        $salary_id = $conn->insert_id; // Get the auto-generated salary ID
    } else {
        // Salary exists, fetch its ID
        $row = $result->fetch_assoc();
        $salary_id = $row["salary_id"];
    }

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

    // Redirect back to the previous page or any other page as needed
    header("Location: previous_page.php");
    exit();
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job Listing</title>
</head>
<body>
    <h2>Add Job Listing</h2>
    <form action="submit_job.php" method="post">
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
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>
