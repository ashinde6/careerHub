<?php
require 'connect-db.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Insert user into database
    $stmt = $db->prepare("INSERT INTO User (name, username, password, email, role, company_id) VALUES (?, ?, ?, ?, ?, NULL)");
    $stmt->execute([$name, $username, $password, $email, $user_type]);

    // Retrieve user_id of the inserted user
    $user_id = $db->lastInsertId();

    // Store user_id in session
    $_SESSION['user_id'] = $user_id;

    // Check if the user is an Employer
    if ($user_type === 'employer') {
        // insert Description
        $description = $_POST['description'];
        $stmt = $db->prepare("INSERT INTO Description (description) VALUES (?)");
        $stmt->execute([$description]);
        $description_id = $db->lastInsertId();
        // insert Location
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip = $_POST['zip'];
        $country = $_POST['country'];
        $stmt = $db->prepare("INSERT INTO Location (address_country, address_state, address_zip, address_city) VALUES (?, ?, ?, ?)");
        $stmt->execute([$country, $state, $zip, $city]);
        $location_id = $db->lastInsertId();
        // insert Company
        $company_name = $_POST['company_name'];
        $speciality = $_POST['speciality'];
        $employee_count = $_POST['employee_count'];
        $stmt = $db->prepare("INSERT INTO Company (name, speciality, employee_count, location_id, description_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$company_name, $speciality, $employee_count, $location_id, $description_id]);
        $company_id = $db->lastInsertId();

        // Update user's company_id in User table
        $stmt = $db->prepare("UPDATE User SET company_id = ? WHERE user_id = ?");
        $stmt->execute([$company_id, $user_id]);

        header("Location: login.php");
        exit();
    } else {
        // If user is not an employer, do something else (maybe redirect to a different page)
        header("Location: login.php");
        exit();
    }
}
?>

<html>
<head>
  <meta charset="utf-8">   
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!-- required to handle IE -->
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>CareerHub</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
  <link rel="stylesheet" href="/static/styling/maintenance-system.css" /> 
  <script>
    function toggleFields() {
        var userType = document.getElementById('user_type').value;
        var employerFields = document.getElementById('employer_fields');

        if (userType === 'employer') {
            employerFields.style.display = 'block';
        } else {
            employerFields.style.display = 'none';
        }
    }
  </script>
</head>
<body>  
  <div>  
    <h1>CareerHub</h1>
    <form action="signup.php" method="post">
      Name: <input type="text" name="name" required /> <br />     
      Username: <input type="text" name="username" required /> <br/>
      Password: <input type="password" name="pwd" required /> <br/>
      Email: <input type="email" name="email" required /> <br />
      Role: <select id="user_type" name="user_type" onchange="toggleFields()" required>
                <option value="" disabled selected>-- Select an Option --</option>
                <option value="job_seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select> <br />
      <div id="employer_fields" style="display: none;">
          Company Name: <input type="text", name="company_name" /><br>
          Speciality: <input type="text" name="speciality" /><br>
          Employee Count: <input type="number" name="employee_count" /><br>
          About The Company: <input type="text", name="description" /><br>
          Location - <br>
            City: <input type="text" name="city" /><br>
            State: <input type="text" name="state" /><br>
            Zip: <input type="text" name="zip" /><br>
            Country: <input type="text" name="country" /><br>
      </div>
      <input type="submit" value="Submit" class="btn" />
      <a href="login.php" class="btn">Back</a>
    </form>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
</body>
</html>
