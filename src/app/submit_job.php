<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file here
    include "db_connection.php";

    // Get form data
    $job_name = $_POST["job_name"];
    $work_type = $_POST["work_type"];
    $industry_id = $_POST["industry_id"];
    $description_id = $_POST["description_id"];
    $company_id = $_POST["company_id"];
    $salary_id = $_POST["salary_id"];

    // SQL query to insert new job listing
    $sql = "INSERT INTO job_listings (job_name, work_type, industry_id, description_id, company_id, salary_id)
            VALUES ('$job_name', '$work_type', $industry_id, $description_id, $company_id, $salary_id)";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
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
        
        <label for="industry_id">Industry:</label><br>
        <input type="number" id="industry_id" name="industry_id"><br>
        
        <label for="description_id">Description:</label><br>
        <input type="number" id="description_id" name="description_id"><br>
        
        <label for="salary_id">Salary:</label><br>
        <input type="number" id="salary_id" name="salary_id"><br>

        <label for="application_url">Application URL:</label><br>
        <input type="text" id="application_url" name="application_url"><br>
        
        <label for="job_posting_url">Job Posting URL:</label><br>
        <input type="text" id="job_posting_url" name="job_posting_url"><br>
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>
